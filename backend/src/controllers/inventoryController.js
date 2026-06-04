const Asset = require('../models/Asset');
const ProcurementDraft = require('../models/ProcurementDraft');
const ProcurementItem = require('../models/ProcurementItem');

/**
 * GET /api/staf-admin/procurements
 * Ambil semua draf pengadaan yang sudah dikunci oleh kaprodi
 */
const getLockedDrafts = async (req, res) => {
    try {
        const drafts = await ProcurementDraft.find({ status: 'locked' })
            .populate('createdBy', 'name email')
            .populate('reviewedBy', 'name email')
            .sort({ lockedAt: -1 });
        res.json({ success: true, count: drafts.length, data: drafts });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/staf-admin/procurements/:id
 * Lihat detail draf yang sudah dikunci beserta item-item yang sudah disetujui
 */
const getLockedDraftDetail = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOne({ _id: req.params.id, status: 'locked' })
            .populate('createdBy', 'name email').populate('reviewedBy', 'name email');
        if (!draft) return res.status(404).json({ success: false, message: 'Locked draft not found' });

        const items = await ProcurementItem.find({ draft: draft._id, approvalStatus: 'approved' })
            .populate('replacedAsset', 'name assetCode');

        res.json({ success: true, data: { ...draft.toObject(), items } });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/staf-admin/assets
 * Ambil daftar semua barang inventaris laboratorium
 */
const getAllAssets = async (req, res) => {
    try {
        const assets = await Asset.find()
            .populate('room', 'name code')
            .populate('replacedAsset', 'name assetCode')
            .sort({ createdAt: -1 });
        res.json({ success: true, count: assets.length, data: assets });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PATCH /api/staf-admin/assets/:id/label
 * Update kode aset, foto label, atau QR code barang
 * Body: { assetCode, labelPhoto, qrCode }
 */
const updateAssetLabel = async (req, res) => {
    try {
        const { assetCode, labelPhoto, qrCode } = req.body;
        const asset = await Asset.findByIdAndUpdate(
            req.params.id,
            { assetCode, labelPhoto, qrCode },
            { new: true, runValidators: true }
        );
        if (!asset) return res.status(404).json({ success: false, message: 'Asset not found' });
        res.json({ success: true, data: asset });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PATCH /api/staf-admin/assets/:id/receive
 * Catat tanggal barang diterima secara fisik
 * Body: { receivedDate }
 * PENTING: Setelah barang diterima, tanggal tidak bisa diubah lagi!
 */
const setReceivedDate = async (req, res) => {
    try {
        const { receivedDate } = req.body;
        if (!receivedDate) {
            return res.status(400).json({ success: false, message: 'receivedDate is required' });
        }

        const asset = await Asset.findById(req.params.id);
        if (!asset) return res.status(404).json({ success: false, message: 'Asset not found' });

        // Jika barang sudah pernah diterima, tidak boleh mengubah tanggalnya
        if (asset.receivedDate) {
            return res.status(400).json({ 
                success: false, 
                message: 'Barang sudah diterima. Tanggal penerimaan tidak bisa diubah.',
                data: { currentReceivedDate: asset.receivedDate }
            });
        }

        asset.receivedDate = new Date(receivedDate);
        await asset.save();
        res.json({ success: true, data: asset });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/.../assets/scan/:code
 * Cari aset berdasarkan assetCode atau qrCode
 */
const getAssetByCode = async (req, res) => {
    try {
        const { code } = req.params;
        const asset = await Asset.findOne({
            $or: [{ assetCode: code }, { qrCode: code }]
        })
        .populate('room', 'name code')
        .populate('replacedAsset', 'name assetCode');

        if (!asset) {
            return res.status(404).json({ success: false, message: 'Asset not found' });
        }
        res.json({ success: true, data: asset });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * POST /api/.../assets
 * Tambah barang baru (biasanya hasil dari scan barcode baru)
 * Body: { name, category, room, assetCode, ... }
 */
const createAsset = async (req, res) => {
    try {
        const newAsset = new Asset(req.body);
        await newAsset.save();
        res.status(201).json({ success: true, data: newAsset });
    } catch (error) {
        res.status(400).json({ success: false, message: error.message });
    }
};

module.exports = { 
    getLockedDrafts, 
    getLockedDraftDetail, 
    getAllAssets, 
    updateAssetLabel, 
    setReceivedDate,
    getAssetByCode,
    createAsset
};
