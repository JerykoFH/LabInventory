const ConsumableItem = require('../models/ConsumableItem');
const MaintenanceLog = require('../models/MaintenanceLog');
const Asset = require('../models/Asset');

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
            return res.status(400).json({ success: false, message: 'Insufficient stock' });
        }

        item.currentStock = newStock;
        await item.save();
        res.json({ success: true, data: item });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

// Mencatat dan melacak pemeliharaan aset laboratorium

/**
 * GET /api/staf-lab/maintenance
 * Ambil semua catatan pemeliharaan aset yang sudah dilakukan
 */
const getAllMaintenanceLogs = async (req, res) => {
    try {
        const logs = await MaintenanceLog.find()
            .populate('asset', 'name assetCode')
            .populate('room', 'name code')
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
 * Catat pemeliharaan aset baru dan otomatis kurangi stok BHP yang dipakai
 * Body: {
 *   asset, room, maintenanceDate, type, description,
 *   conditionBefore, conditionAfter, notes,
 *   consumablesUsed: [{ item: id, quantityUsed: number }]
 * }
 */
const createMaintenanceLog = async (req, res) => {
    try {
        const { asset, consumablesUsed, conditionAfter, ...rest } = req.body;

        // Pastikan barang (asset) yang dimaksud benar-benar ada
        const assetDoc = await Asset.findById(asset);
        if (!assetDoc) return res.status(404).json({ success: false, message: 'Asset not found' });

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

        // Perbarui kondisi barang setelah pemeliharaan
        if (conditionAfter) {
            assetDoc.condition = conditionAfter;
            // Jika barangnya rusak berat atau tidak bisa diperbaiki, tandai sebagai tidak aktif
            if (conditionAfter === 'tidak_aktif') assetDoc.status = 'tidak_aktif';
            await assetDoc.save();
        }

        const log = await MaintenanceLog.create({
            asset,
            performedBy: req.user._id,
            consumablesUsed: consumablesUsed || [],
            conditionAfter,
            ...rest,
        });

        const populated = await log.populate([
            { path: 'asset', select: 'name assetCode' },
            { path: 'room', select: 'name code' },
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
            .populate('asset', 'name assetCode condition')
            .populate('room', 'name code')
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
    getAllMaintenanceLogs, createMaintenanceLog, getMaintenanceLogById,
};
