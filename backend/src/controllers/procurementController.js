const ProcurementDraft = require('../models/ProcurementDraft');
const ProcurementItem = require('../models/ProcurementItem');

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

        draft.status = 'submitted';
        draft.submittedAt = new Date();
        await draft.save();
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

        // Hapus item-item di dalamnya juga
        await ProcurementItem.deleteMany({ draft: draft._id });
        await draft.deleteOne();
        
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

        await ProcurementItem.findOneAndDelete({ _id: req.params.itemId, draft: draft._id });
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
