const express = require('express');
const router = express.Router();
const { protect, authorize } = require('../middleware/authMiddleware');

// Controller imports — reuse existing read-only functions
const { getAllAssets } = require('../controllers/inventoryController');
const { getAllConsumables, getAllRooms, getAssetsByRoom } = require('../controllers/labController');

// Semua role boleh akses kecuali admin
router.use(protect, authorize('kepala_lab', 'kaprodi', 'staf_admin', 'staf_lab'));

// Inventaris (read-only)
router.get('/assets', getAllAssets);

// BHP (read-only)
router.get('/consumables', getAllConsumables);

// Ruangan & aset per ruangan (read-only)
router.get('/rooms', getAllRooms);
router.get('/rooms/:id/assets', getAssetsByRoom);

// History (Activity Log)
const { getHistory } = require('../controllers/historyController');
router.get('/history', getHistory);

module.exports = router;
