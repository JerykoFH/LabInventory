const ConsumableItem = require('../models/ConsumableItem');
const MaintenanceLog = require('../models/MaintenanceLog');
const Asset = require('../models/Asset');
const Room = require('../models/Room');

// Mengelola stok barang habis pakai (BHP) — tambah, kurangi, lihat stok

/**
 * GET /api/staf-lab/consumables
 * Ambil daftar semua barang habis pakai beserta stok terkini
 */
const getAllConsumables = async (req, res) => {
    try {
        const items = await ConsumableItem.find().sort({ name: 1 });
        res.json({ success: true, count: items.length, data: items });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * POST /api/staf-lab/consumables
 * Daftarkan barang habis pakai baru
 * Body: { name, category?, unit, currentStock?, minimumStock?, location?, notes? }
 * Catatan: Stok dimulai dari 0 jika tidak ditentukan; minimumStock bersifat opsional
 */
const createConsumable = async (req, res) => {
    try {
        const { name, unit, currentStock, minimumStock, ...rest } = req.body;
        
        if (!name || !unit) {
            return res.status(400).json({ success: false, message: 'name and unit are required' });
        }

        const item = await ConsumableItem.create({
            name,
            unit,
            currentStock: currentStock !== undefined ? currentStock : 0,
            minimumStock: minimumStock !== undefined ? minimumStock : 5,
            ...rest
        });
        res.status(201).json({ success: true, data: item });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PATCH /api/staf-lab/consumables/:id/stock
 * Sesuaikan stok barang habis pakai (tambah atau kurangi)
 * Body: { adjustment: number, reason? }
 *   Nilai positif = menambah stok, Nilai negatif = mengurangi stok
 */
const adjustStock = async (req, res) => {
    try {
        const { adjustment, reason } = req.body;
        if (adjustment === undefined) {
            return res.status(400).json({ success: false, message: 'adjustment is required' });
        }

        const item = await ConsumableItem.findById(req.params.id);
        if (!item) return res.status(404).json({ success: false, message: 'Consumable item not found' });

        const newStock = item.currentStock + Number(adjustment);
        if (newStock < 0) {
            return res.status(400).json({ success: false, message: 'Stok tidak mencukupi untuk pengurangan ini' });
        }

        item.currentStock = newStock;
        await item.save();
        res.json({ success: true, data: item });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

// Room Management

/**
 * GET /api/staf-lab/rooms
 * Lihat semua ruangan untuk pilihan maintenance
 */
const getAllRooms = async (req, res) => {
    try {
        const rooms = await Room.find({ isActive: true }).sort({ name: 1 });
        res.json({ success: true, count: rooms.length, data: rooms });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/staf-lab/rooms/:id/assets
 * Lihat semua aset dalam satu ruangan
 */
const getAssetsByRoom = async (req, res) => {
    try {
        const assets = await Asset.find({ room: req.params.id }).sort({ name: 1 });
        res.json({ success: true, count: assets.length, data: assets });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

// Maintenance Log 

/**
 * GET /api/staf-lab/maintenance
 * Ambil semua catatan pemeliharaan aset yang sudah dilakukan
 */
const getAllMaintenanceLogs = async (req, res) => {
    try {
        const logs = await MaintenanceLog.find()
            .populate('room', 'name code')
            .populate('assets.asset', 'name assetCode')
            .populate('performedBy', 'name')
            .populate('consumablesUsed.item', 'name unit')
            .sort({ maintenanceDate: -1 });
        res.json({ success: true, count: logs.length, data: logs });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * POST /api/staf-lab/maintenance
 * Catat pemeliharaan aset baru (mendukung banyak aset) dan otomatis kurangi stok BHP yang dipakai
 */
const createMaintenanceLog = async (req, res) => {
    try {
        let room, consumablesUsed, type, description, notes, maintenanceDate;
        let assets = [];

        if (req.body.data) {
            // Jika dikirim sebagai form-data multipart (karena ada file upload gambar)
            const parsed = JSON.parse(req.body.data);
            room = parsed.room;
            consumablesUsed = parsed.consumablesUsed;
            type = parsed.type;
            description = parsed.description;
            notes = parsed.notes;
            maintenanceDate = parsed.maintenanceDate;
            assets = parsed.assets || [];
        } else {
            // Request JSON normal
            ({ room, assets, consumablesUsed, type, description, notes, maintenanceDate } = req.body);
            assets = assets || [];
        }

        // Pastikan assets selalu berupa array
        if (!Array.isArray(assets)) {
            assets = Object.values(assets);
        }

        // Pastikan ruangan (room) yang dimaksud benar-benar ada
        const roomDoc = await Room.findById(room);
        if (!roomDoc) return res.status(404).json({ success: false, message: 'Room not found' });

        // Validasi assets dan ambil ID-nya saja untuk cek ketersediaan
        const assetIds = assets.map(a => a.asset).filter(id => id);
        if (assetIds.length > 0) {
            const assetDocs = await Asset.find({ _id: { $in: assetIds } });
            if (assetDocs.length !== assetIds.length) {
                return res.status(404).json({ success: false, message: 'Some assets not found' });
            }
        }

        // Handle uploaded files mapping (menautkan file foto ke aset yang sesuai)
        if (req.files && req.files.length > 0) {
            req.files.forEach(file => {
                const match = file.fieldname.match(/^(photoBefore|photoAfter)_(\d+)$/);
                if (match) {
                    const field = match[1]; // photoBefore or photoAfter
                    const index = parseInt(match[2], 10);
                    if (assets[index]) {
                        // Store the URL path
                        assets[index][ field === 'photoBefore' ? 'conditionPhotoBefore' : 'conditionPhotoAfter' ] = `/uploads/maintenance/${file.filename}`;
                    }
                }
            });
        }

        // Proses pengurangan stok untuk setiap BHP yang digunakan
        if (consumablesUsed && consumablesUsed.length > 0) {
            for (const usage of consumablesUsed) {
                const consumable = await ConsumableItem.findById(usage.item);
                if (!consumable) {
                    return res.status(404).json({ success: false, message: `Consumable ${usage.item} not found` });
                }
                if (consumable.currentStock < usage.quantityUsed) {
                    return res.status(400).json({
                        success: false,
                        message: `Insufficient stock for ${consumable.name}`,
                    });
                }
                consumable.currentStock -= usage.quantityUsed;
                await consumable.save();
            }
        }

        // Perbarui kondisi masing-masing barang setelah pemeliharaan
        if (assets.length > 0) {
            for (const assetObj of assets) {
                if (assetObj.conditionAfter) {
                    const assetDoc = await Asset.findById(assetObj.asset);
                    if (assetDoc) {
                        assetDoc.condition = assetObj.conditionAfter;
                        // Jika barangnya rusak berat atau tidak bisa diperbaiki, tandai sebagai tidak aktif
                        if (assetObj.conditionAfter === 'tidak_aktif') assetDoc.status = 'tidak_aktif';
                        await assetDoc.save();
                    }
                }
            }
        }

        const log = await MaintenanceLog.create({
            room,
            assets: assets,
            performedBy: req.user._id,
            consumablesUsed: consumablesUsed || [],
            type,
            description,
            notes,
            maintenanceDate,
        });

        const populated = await log.populate([
            { path: 'room', select: 'name code' },
            { path: 'assets.asset', select: 'name assetCode' },
            { path: 'consumablesUsed.item', select: 'name unit' },
        ]);

        res.status(201).json({ success: true, data: populated });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/staf-lab/maintenance/:id
 * Lihat detail lengkap satu catatan pemeliharaan
 */
const getMaintenanceLogById = async (req, res) => {
    try {
        const log = await MaintenanceLog.findById(req.params.id)
            .populate('room', 'name code location')
            .populate('assets.asset', 'name assetCode condition')
            .populate('performedBy', 'name email')
            .populate('consumablesUsed.item', 'name unit');
        if (!log) return res.status(404).json({ success: false, message: 'Log not found' });
        res.json({ success: true, data: log });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

module.exports = {
    getAllConsumables, createConsumable, adjustStock,
    getAllRooms, getAssetsByRoom,
    getAllMaintenanceLogs, createMaintenanceLog, getMaintenanceLogById,
};
