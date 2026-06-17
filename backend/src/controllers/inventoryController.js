const Asset = require('../models/Asset');
const ConsumableItem = require('../models/ConsumableItem');
const ProcurementDraft = require('../models/ProcurementDraft');
const ProcurementItem = require('../models/ProcurementItem');
const { logActivity } = require('../utils/logger');

/**
 * GET /api/staf-admin/procurements
 * Ambil semua draf pengadaan yang sudah dikunci oleh kaprodi
 */
const getLockedDrafts = async (req, res) => {
    try {
        const drafts = await ProcurementDraft.find({ status: { $in: ['locked', 'in_progress'] } })
            .populate('createdBy', 'name email')
            .populate('reviewedBy', 'name email')
            .sort({ lockedAt: -1 })
            .lean();

        const draftsWithItems = [];
        for (let draft of drafts) {
            const itemCount = await ProcurementItem.countDocuments({ draft: draft._id, approvalStatus: 'approved' });
            if (itemCount > 0) {
                draft.itemCount = itemCount;
                draftsWithItems.push(draft);
            }
        }

        res.json({ success: true, count: draftsWithItems.length, data: draftsWithItems });
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
        const { received, category, condition, status, room, search, startDate, endDate } = req.query;
        
        // Filter berdasarkan status penerimaan
        if (received === 'true') {
            filter.receivedDate = { $ne: null };
        } else if (received === 'false') {
            filter.receivedDate = null;
        }

        if (startDate || endDate) {
            filter.receivedDate = filter.receivedDate || {};
            if (startDate) {
                const sDate = new Date(startDate);
                sDate.setHours(0, 0, 0, 0);
                filter.receivedDate.$gte = sDate;
            }
            if (endDate) {
                const eDate = new Date(endDate);
                eDate.setHours(23, 59, 59, 999);
                filter.receivedDate.$lte = eDate;
            }
        }

        if (category) filter.category = category;
        if (condition) filter.condition = condition;
        if (status) filter.status = status;
        if (room) filter.room = room;
        if (search) {
            filter.$or = [
                { name: { $regex: search, $options: 'i' } },
                { assetCode: { $regex: search, $options: 'i' } }
            ];
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
        const { assetCode, labelPhoto, qrCode, room } = req.body;
        
        const updateData = { assetCode, labelPhoto, qrCode };
        
        if (room !== undefined) {
            updateData.room = room || null;
        }

        const asset = await Asset.findByIdAndUpdate(
            req.params.id,
            updateData,
            { new: true, runValidators: true }
        );
        if (!asset) return res.status(404).json({ success: false, message: 'Asset not found' });
        
        await logActivity(req, 'UPDATE', 'Asset', asset._id, `Memperbarui label aset: ${asset.name}`, { assetCode: updateData.assetCode, qrCode: updateData.qrCode });
        
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
        
        await logActivity(req, 'UPDATE', 'Asset', asset._id, `Mencatat penerimaan aset: ${asset.name}`, { receivedDate });
        
        res.json({ success: true, data: asset });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PATCH /api/staf-admin/assets/:id/condition
 * Update kondisi barang
 * Body: { condition }
 */
const updateAssetCondition = async (req, res) => {
    try {
        const { condition } = req.body;
        if (!condition) {
            return res.status(400).json({ success: false, message: 'Kondisi wajib diisi' });
        }

        const validConditions = ['baik', 'rusak_ringan', 'rusak_berat', 'tidak_aktif'];
        if (!validConditions.includes(condition)) {
            return res.status(400).json({ success: false, message: 'Kondisi tidak valid' });
        }

        const asset = await Asset.findByIdAndUpdate(
            req.params.id,
            { condition },
            { new: true, runValidators: true }
        );
        
        if (!asset) return res.status(404).json({ success: false, message: 'Asset not found' });
        
        await logActivity(req, 'UPDATE', 'Asset', asset._id, `Memperbarui kondisi aset ${asset.name} menjadi ${condition}`, { condition });
        
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
        
        await logActivity(req, 'CREATE', 'Asset', newAsset._id, `Menambahkan aset inventaris baru: ${newAsset.name}`);
        
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
        
        await logActivity(req, 'UPDATE', 'ProcurementDraft', draft._id, `Memulai proses pengadaan (in_progress)`);
        
        res.json({ success: true, data: draft });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

const receiveProcurementItem = async (req, res) => {
    try {
        const { receivedQuantity, room } = req.body;
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

        const previousReceivedQuantity = item.receivedQuantity || 0;
        const newlyReceived = receivedQuantity - previousReceivedQuantity;

        item.receivedQuantity = receivedQuantity;
        await item.save();

        if (newlyReceived > 0) {
            if (item.itemType === 'asset') {
                let roomObj = null;
                let prefix = 'INV-IT';
                if (room) {
                    const Room = require('../models/Room');
                    roomObj = await Room.findById(room);
                    if (roomObj) prefix = `INV-${roomObj.code}`;
                }

                const assetsToCreate = [];
                for (let i = 0; i < newlyReceived; i++) {
                    const uniqueId = Date.now().toString().slice(-6) + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                    const generatedCode = `${prefix}-${uniqueId}`;
                    
                    assetsToCreate.push({
                        name: item.name,
                        category: 'Aset Baru',
                        assetCode: generatedCode,
                        qrCode: generatedCode,
                        room: room || null,
                        condition: 'baik',
                        status: 'aktif',
                        purchaseDate: draft.lockedAt || new Date(),
                        purchasePrice: item.estimatedPrice,
                        receivedDate: new Date(),
                        procurementItem: item._id,
                        replacedAsset: item.replacedAsset
                    });
                }
                if (assetsToCreate.length > 0) {
                    await Asset.insertMany(assetsToCreate);
                }
            } else if (item.itemType === 'consumable') {
                const existingConsumable = await ConsumableItem.findOne({ name: item.name });
                if (existingConsumable) {
                    existingConsumable.currentStock += newlyReceived;
                    existingConsumable.lastRestockDate = new Date();
                    await existingConsumable.save();
                } else {
                    await ConsumableItem.create({
                        name: item.name,
                        category: 'BHP Baru',
                        unit: item.unit || 'unit',
                        currentStock: newlyReceived,
                        minimumStock: 5,
                        location: 'Gudang',
                        lastRestockDate: new Date()
                    });
                }
            }
        }

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
            await logActivity(req, 'UPDATE', 'ProcurementDraft', draft._id, `Menyelesaikan pengadaan (Semua item telah diterima)`);
        }

        await logActivity(req, 'UPDATE', 'ProcurementItem', item._id, `Menerima ${receivedQuantity} unit item pengadaan: ${item.name}`, { receivedQuantity });

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
    updateAssetCondition,
    getAssetByCode,
    createAsset,
    setProcurementProgress,
    receiveProcurementItem
};
