const express = require('express');
const router = express.Router();
const { protect, authorize } = require('../middleware/authMiddleware');
const {
    getAllConsumables, createConsumable, adjustStock,
    getAllRooms, getAssetsByRoom,
    getAllMaintenanceLogs, createMaintenanceLog, getMaintenanceLogById,
} = require('../controllers/labController');

router.use(protect, authorize('staf_lab'));

// BHP
router.get('/consumables', getAllConsumables);
router.post('/consumables', createConsumable);
router.patch('/consumables/:id/stock', adjustStock);

// Rooms
router.get('/rooms', getAllRooms);
router.get('/rooms/:id/assets', getAssetsByRoom);

const upload = require('../middleware/uploadMiddleware');

// Maintenance
router.get('/maintenance', getAllMaintenanceLogs);
router.post('/maintenance', upload.any(), createMaintenanceLog);
router.get('/maintenance/:id', getMaintenanceLogById);

module.exports = router;
