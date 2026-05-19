const ConsumableItem = require('../models/ConsumableItem');
const MaintenanceLog = require('../models/MaintenanceLog');
const Asset = require('../models/Asset');

// ── Consumable Stock Management ──────────────────────────────────────────────

/**
 * GET /api/staf-lab/consumables
 * Lihat semua BHP beserta stok
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
 * Tambah item BHP baru
 */
const createConsumable = async (req, res) => {
    try {
        const item = await ConsumableItem.create(req.body);
        res.status(201).json({ success: true, data: item });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PATCH /api/staf-lab/consumables/:id/stock
 * Update stok BHP (tambah/kurangi)
 * Body: { adjustment: number, reason? }
 *   adjustment > 0 → tambah stok
 *   adjustment < 0 → kurangi stok
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

// ── Maintenance Log ──────────────────────────────────────────────────────────

/**
 * GET /api/staf-lab/maintenance
 * Lihat semua log maintenance
 */
const getAllMaintenanceLogs = async (req, res) => {
    try {
        const logs = await MaintenanceLog.find()
            .populate('asset', 'name assetCode')
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
 * Buat log maintenance baru + kurangi stok BHP yang digunakan
 * Body: {
 *   asset, maintenanceDate, type, description,
 *   conditionBefore, conditionAfter, notes,
 *   consumablesUsed: [{ item: id, quantityUsed: number }]
 * }
 */
const createMaintenanceLog = async (req, res) => {
    try {
        const { asset, consumablesUsed, conditionAfter, ...rest } = req.body;

        // Validasi asset ada
        const assetDoc = await Asset.findById(asset);
        if (!assetDoc) return res.status(404).json({ success: false, message: 'Asset not found' });

        // Kurangi stok BHP yang digunakan
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

        // Update kondisi aset
        if (conditionAfter) {
            assetDoc.condition = conditionAfter;
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
            { path: 'consumablesUsed.item', select: 'name unit' },
        ]);

        res.status(201).json({ success: true, data: populated });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/staf-lab/maintenance/:id
 * Detail satu log maintenance
 */
const getMaintenanceLogById = async (req, res) => {
    try {
        const log = await MaintenanceLog.findById(req.params.id)
            .populate('asset', 'name assetCode condition')
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
