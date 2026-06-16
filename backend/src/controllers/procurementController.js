const ProcurementDraft = require('../models/ProcurementDraft');
const ProcurementItem = require('../models/ProcurementItem');
const User = require('../models/User');
const { logActivity } = require('../utils/logger');
const { sendEmail } = require('../utils/mailer');

/**
 * GET /api/kepala-lab/procurements
 * Lihat semua draf milik kepala lab yang login
 */
const getMyDrafts = async (req, res) => {
    try {
        const drafts = await ProcurementDraft.find({ createdBy: req.user._id })
            .populate('reviewedBy', 'name email')
            .sort({ createdAt: -1 });
        res.json({ success: true, count: drafts.length, data: drafts });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * POST /api/kepala-lab/procurements
 * Buat draf pengadaan baru
 */
const createDraft = async (req, res) => {
    try {
        const { title, year, notes } = req.body;
        const draft = await ProcurementDraft.create({
            title, year, notes,
            createdBy: req.user._id,
            status: 'draft',
        });
        
        await logActivity(req, 'CREATE', 'ProcurementDraft', draft._id, `Membuat draf pengadaan baru: ${title} (${year})`);
        
        res.status(201).json({ success: true, data: draft });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/kepala-lab/procurements/:id
 * Lihat detail draf + items
 */
const getDraftById = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOne({ _id: req.params.id, createdBy: req.user._id })
            .populate('reviewedBy', 'name email');
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });

        const items = await ProcurementItem.find({ draft: draft._id })
            .populate('replacedAsset', 'name assetCode');

        res.json({ success: true, data: { ...draft.toObject(), items } });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PUT /api/kepala-lab/procurements/:id
 * Update draf (hanya jika masih berstatus 'draft')
 */
const updateDraft = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOne({ _id: req.params.id, createdBy: req.user._id });
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });
        if (draft.status === 'locked') {
            return res.status(400).json({ success: false, message: 'Locked draft cannot be modified' });
        }

        const { title, year, notes } = req.body;
        Object.assign(draft, { title, year, notes });
        await draft.save();
        
        await logActivity(req, 'UPDATE', 'ProcurementDraft', draft._id, `Memperbarui draf pengadaan: ${title}`);
        
        res.json({ success: true, data: draft });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * POST /api/kepala-lab/procurements/:id/submit
 * Submit draf ke kaprodi
 */
const submitDraft = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOne({ _id: req.params.id, createdBy: req.user._id });
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });
        if (draft.status !== 'draft') {
            return res.status(400).json({ success: false, message: 'Only drafts with status "draft" can be submitted' });
        }

        const itemsCount = await ProcurementItem.countDocuments({ draft: draft._id });
        if (itemsCount === 0) {
            return res.status(400).json({ success: false, message: 'Draf tidak bisa disubmit karena belum ada barang (kosong).' });
        }

        draft.status = 'submitted';
        draft.submittedAt = new Date();
        await draft.save();
        
        await logActivity(req, 'UPDATE', 'ProcurementDraft', draft._id, `Mengajukan draf pengadaan ke Kaprodi: ${draft.title}`);
        
        // Ambil daftar barang untuk ditampilkan di email
        const items = await ProcurementItem.find({ draft: draft._id });
        let itemsHtml = '';
        if (items.length > 0) {
            const grandTotal = items.reduce((sum, item) => sum + (item.estimatedPrice * item.quantity), 0);

            itemsHtml = `
                <h3 style="color: #333; margin-top: 25px; font-size: 16px;">Daftar Barang yang Diajukan:</h3>
                <table style="border-collapse: collapse; width: 100%; max-width: 600px; margin-bottom: 20px;">
                    <thead>
                        <tr style="background-color: #f2f2f2; text-align: left;">
                            <th style="padding: 10px; border: 1px solid #ddd; width: 50%;">Nama Barang</th>
                            <th style="padding: 10px; border: 1px solid #ddd; text-align: center; width: 20%;">Jumlah</th>
                            <th style="padding: 10px; border: 1px solid #ddd; text-align: right; width: 30%;">Estimasi Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(item => {
                            const subTotal = item.estimatedPrice * item.quantity;
                            return `
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <strong>${item.name}</strong><br>
                                    <span style="font-size: 12px; color: #666;">Tipe: ${item.itemType === 'asset' ? 'Aset Inventaris' : 'Barang Habis Pakai (BHP)'}</span>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                                    ${item.quantity} ${item.unit}
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">
                                    Rp ${item.estimatedPrice.toLocaleString('id-ID')}<br>
                                    <span style="font-size: 11px; color: #000;">Subtotal: Rp ${subTotal.toLocaleString('id-ID')}</span>
                                </td>
                            </tr>
                            `;
                        }).join('')}
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #eaf4fb;">
                            <td colspan="2" style="padding: 10px; border: 1px solid #ddd; text-align: right;"><strong>Total Biaya Keseluruhan:</strong></td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right; color: #d32f2f; font-size: 16px;"><strong>Rp ${grandTotal.toLocaleString('id-ID')}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            `;
        }

        // Kirim email notifikasi ke Kaprodi
        try {
            const kaprodis = await User.find({ role: 'kaprodi', isActive: true });
            kaprodis.forEach(kaprodi => {
                const subject = `Notifikasi Pengadaan Baru: ${draft.title}`;
                const htmlContent = `
                    <div style="font-family: Arial, sans-serif; padding: 20px; color: #333;">
                        <h2 style="color: #2196F3;">Pengajuan Draf Pengadaan Baru</h2>
                        <p>Halo <strong>${kaprodi.name}</strong>,</p>
                        <p>Kepala Laboratorium baru saja mengajukan draf pengadaan baru yang menunggu persetujuan Anda.</p>
                        <table style="border-collapse: collapse; width: 100%; max-width: 600px; margin-top: 15px; margin-bottom: 20px;">
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; width: 30%;"><strong>Judul Pengadaan</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">${draft.title}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Tahun</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">${draft.year}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Catatan</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">${draft.notes || '-'}</td>
                            </tr>
                        </table>
                        
                        ${itemsHtml}

                        <p>Silakan login ke sistem <strong>LabInventory</strong> untuk melihat detail dan melakukan proses review (Approve/Reject) terhadap item-item yang diajukan.</p>
                        <p style="margin-top: 30px; font-size: 12px; color: #777;">Email ini dikirim secara otomatis oleh Sistem LabInventory.</p>
                    </div>
                `;
                sendEmail(kaprodi.email, subject, htmlContent);
            });
        } catch (mailError) {
            console.error('Error saat mencoba mengirim notifikasi:', mailError);
        }

        res.json({ success: true, message: 'Draft submitted for review', data: draft });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * DELETE /api/kepala-lab/procurements/:id
 * Hapus draf pengadaan yang masih berstatus draft
 */
const deleteDraft = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOne({ _id: req.params.id, createdBy: req.user._id });
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });

        // Hanya draf yang belum disubmit yang bisa dihapus
        if (draft.status !== 'draft') {
            return res.status(400).json({ success: false, message: 'Only unsubmitted drafts can be deleted' });
        }

        const draftTitle = draft.title;
        // Hapus item-item di dalamnya juga
        await ProcurementItem.deleteMany({ draft: draft._id });
        await draft.deleteOne();

        await logActivity(req, 'DELETE', 'ProcurementDraft', draft._id, `Menghapus draf pengadaan: ${draftTitle}`);

        res.json({ success: true, message: 'Draft successfully deleted' });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

// ── Procurement Items ────────────────────────────────────────────────────────

/**
 * POST /api/kepala-lab/procurements/:id/items
 * Tambah item ke dalam draf
 */
const addItem = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOne({ _id: req.params.id, createdBy: req.user._id });
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });
        if (draft.status === 'locked') {
            return res.status(400).json({ success: false, message: 'Cannot add items to a locked draft' });
        }

        const item = await ProcurementItem.create({ ...req.body, draft: draft._id });
        
        await logActivity(req, 'CREATE', 'ProcurementItem', item._id, `Menambahkan item ${item.name} ke draf ${draft.title}`);
        
        res.status(201).json({ success: true, data: item });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PUT /api/kepala-lab/procurements/:id/items/:itemId
 * Update item dalam draf
 */
const updateItem = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOne({ _id: req.params.id, createdBy: req.user._id });
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });
        if (draft.status === 'locked') {
            return res.status(400).json({ success: false, message: 'Cannot edit items of a locked draft' });
        }

        const item = await ProcurementItem.findOneAndUpdate(
            { _id: req.params.itemId, draft: draft._id },
            req.body,
            { new: true, runValidators: true }
        );
        if (!item) return res.status(404).json({ success: false, message: 'Item not found' });
        
        await logActivity(req, 'UPDATE', 'ProcurementItem', item._id, `Memperbarui item ${item.name} di draf ${draft.title}`);
        
        res.json({ success: true, data: item });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * DELETE /api/kepala-lab/procurements/:id/items/:itemId
 * Hapus item dari draf
 */
const deleteItem = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOne({ _id: req.params.id, createdBy: req.user._id });
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });
        if (draft.status === 'locked') {
            return res.status(400).json({ success: false, message: 'Cannot delete items from a locked draft' });
        }

        const item = await ProcurementItem.findOneAndDelete({ _id: req.params.itemId, draft: draft._id });
        if (item) {
            await logActivity(req, 'DELETE', 'ProcurementItem', item._id, `Menghapus item ${item.name} dari draf ${draft.title}`);
        }
        res.json({ success: true, message: 'Item removed' });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

module.exports = {
    getMyDrafts,
    createDraft,
    getDraftById,
    updateDraft,
    submitDraft,
    deleteDraft,
    addItem,
    updateItem,
    deleteItem,
};
