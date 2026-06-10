const Asset = require('../models/Asset');
const ProcurementDraft = require('../models/ProcurementDraft');
const ProcurementItem = require('../models/ProcurementItem');

/**
 * GET /api/staf-admin/procurements
 * Ambil semua draf pengadaan yang sudah dikunci oleh kaprodi
 */
const getLockedDrafts = async (req, res) => {
    try {
        const drafts = await ProcurementDraft.find({ status: { $in: ['locked', 'in_progress'] } })
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
        const draft = await ProcurementDraft.findOne({ _id: req.params.id, status: { $in: ['locked', 'in_progress'] } })
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

const setReceivedDate = async (req, res) => {
    try {
        const { receivedDate } = req.body;
        if (!receivedDate) {
            return res.status(400).json({ success: false, message: 'Tanggal penerimaan wajib diisi' });
        }
        
        const asset = await Asset.findByIdAndUpdate(
            req.params.id,
            { receivedDate },
            { new: true, runValidators: true }
        );
        
        if (!asset) return res.status(404).json({ success: false, message: 'Asset not found' });
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

const setProcurementProgress = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOneAndUpdate(
            { _id: req.params.id, status: 'locked' },
            { status: 'in_progress' },
            { new: true }
        );
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found or not locked' });
        res.json({ success: true, data: draft });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

const receiveProcurementItem = async (req, res) => {
    try {
        const { receivedQuantity } = req.body;
        if (receivedQuantity === undefined || receivedQuantity < 0) {
            return res.status(400).json({ success: false, message: 'Invalid received quantity' });
        }

        const draft = await ProcurementDraft.findOne({ _id: req.params.id, status: 'in_progress' });
        if (!draft) {
            return res.status(404).json({ success: false, message: 'Draft not found or not in progress' });
        }

        const item = await ProcurementItem.findOne({ _id: req.params.itemId, draft: draft._id });
        if (!item) {
            return res.status(404).json({ success: false, message: 'Item not found' });
        }

        if (receivedQuantity > item.quantity) {
            return res.status(400).json({ success: false, message: 'Received quantity cannot exceed ordered quantity' });
        }

        item.receivedQuantity = receivedQuantity;
        await item.save();

        const allItems = await ProcurementItem.find({ draft: draft._id, approvalStatus: 'approved' });
        let allReceived = true;
        for (const i of allItems) {
            if ((i.receivedQuantity || 0) < i.quantity) {
                allReceived = false;
                break;
            }
        }

        if (allReceived && allItems.length > 0) {
            draft.status = 'completed';
            await draft.save();
        }

        res.json({ success: true, data: { item, draftStatus: draft.status } });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

module.exports = { 
    getLockedDrafts, 
    getLockedDraftDetail, 
    getAllAssets, 
    getAssetById,
    updateAssetLabel, 
    setReceivedDate,
    getAssetByCode,
    createAsset,
    setProcurementProgress,
    receiveProcurementItem
};
