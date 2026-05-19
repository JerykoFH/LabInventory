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
 * Input tanggal penerimaan barang
 * Body: { receivedDate }
 */
const setReceivedDate = async (req, res) => {
    try {
        const { receivedDate } = req.body;
        if (!receivedDate) {
            return res.status(400).json({ success: false, message: 'receivedDate is required' });
        }

        const asset = await Asset.findByIdAndUpdate(
            req.params.id,
            { receivedDate: new Date(receivedDate) },
            { new: true }
        );
        if (!asset) return res.status(404).json({ success: false, message: 'Asset not found' });
        res.json({ success: true, data: asset });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

module.exports = { getLockedDrafts, getLockedDraftDetail, getAllAssets, updateAssetLabel, setReceivedDate };
