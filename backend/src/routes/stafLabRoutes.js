const express = require('express');
const router = express.Router();
const { protect, authorize } = require('../middleware/authMiddleware');

// Impor dari labController
const {
    getAllConsumables, createConsumable, adjustStock,
    getAllRooms, getAssetsByRoom,
    getAllMaintenanceLogs, createMaintenanceLog, getMaintenanceLogById,
} = require('../controllers/labController');

// Impor dari inventoryController
const { getAllAssets, getAssetByCode, createAsset } = require('../controllers/inventoryController');

// Perbaikan Error Crash: Impor getAllRooms dari roomController dihapus karena 
// getAllRooms sudah kita layani lewat labController di atas.
// Jika di masa depan ingin pakai roomController, pastikan hapus yang di labController.

router.use(protect, authorize('staf_lab'));

// Aset
router.get('/assets', getAllAssets);
router.get('/assets/scan/:code', getAssetByCode);
router.post('/assets', createAsset);

// BHP
router.get('/consumables', getAllConsumables);
router.post('/consumables', createConsumable);
router.patch('/consumables/:id/stock', adjustStock);

// Rooms (untuk dropdown di form maintenance dan dynamic fetching)
router.get('/rooms', getAllRooms);
router.get('/rooms/:id/assets', getAssetsByRoom);

// Middleware Upload File untuk foto aset
const upload = require('../middleware/uploadMiddleware');

// Maintenance
router.get('/maintenance', getAllMaintenanceLogs);
router.post('/maintenance', upload.any(), createMaintenanceLog);
router.get('/maintenance/:id', getMaintenanceLogById);

module.exports = router;
