const Asset = require('../models/Asset');
const ProcurementDraft = require('../models/ProcurementDraft');
const ProcurementItem = require('../models/ProcurementItem');

/**
 * GET /api/staf-admin/procurements
 * Lihat draf yang sudah locked (disetujui kaprodi)
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
 * Lihat detail draf locked + items yang approved
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
 * Lihat semua inventaris
 * Query: ?received=true untuk hanya barang yang sudah diterima, ?received=false untuk belum diterima
 */
const getAllAssets = async (req, res) => {
    try {
        let filter = {};
        const { received } = req.query;
        
        // Filter berdasarkan status penerimaan
        if (received === 'true') {
            filter.receivedDate = { $ne: null };
        } else if (received === 'false') {
            filter.receivedDate = null;
        }

        const assets = await Asset.find(filter)
            .populate('room', 'name code')
            .populate('replacedAsset', 'name assetCode')
            .sort({ createdAt: -1 });
        res.json({ success: true, count: assets.length, data: assets });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/staf-admin/assets/:id
 * Lihat detail lengkap satu aset dengan semua informasi (label, QR, tanggal terima, dll)
 */
const getAssetById = async (req, res) => {
    try {
        const asset = await Asset.findById(req.params.id)
            .populate('room', 'name code')
            .populate('replacedAsset', 'name assetCode');
        if (!asset) return res.status(404).json({ success: false, message: 'Asset not found' });
        res.json({ success: true, data: asset });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PATCH /api/staf-admin/assets/:id/label
 * Update label / QR / barcode aset
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
 * DEPRECATED: Fitur ini sudah tidak digunakan
 * Tanggal penerimaan diatur otomatis oleh sistem saat barang masuk dari procurement
 */
const setReceivedDate = async (req, res) => {
    return res.status(403).json({ 
        success: false, 
        message: 'Operasi tidak diizinkan. Tanggal penerimaan diatur otomatis oleh sistem.' 
    });
};

module.exports = { getLockedDrafts, getLockedDraftDetail, getAllAssets, getAssetById, updateAssetLabel, setReceivedDate };
