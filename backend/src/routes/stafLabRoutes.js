const express = require('express');
const router = express.Router();
const { protect, authorize } = require('../middleware/authMiddleware');
const {
    getAllConsumables, createConsumable, adjustStock,
    getAllMaintenanceLogs, createMaintenanceLog, getMaintenanceLogById,
} = require('../controllers/labController');
const { getAllAssets, getAssetByCode, createAsset } = require('../controllers/inventoryController');

router.use(protect, authorize('staf_lab'));

// Aset
router.get('/assets', getAllAssets);
router.get('/assets/scan/:code', getAssetByCode);
router.post('/assets', createAsset);

// BHP
router.get('/consumables', getAllConsumables);
router.post('/consumables', createConsumable);
router.patch('/consumables/:id/stock', adjustStock);

// Maintenance
router.get('/maintenance', getAllMaintenanceLogs);
router.post('/maintenance', createMaintenanceLog);
router.get('/maintenance/:id', getMaintenanceLogById);

module.exports = router;
