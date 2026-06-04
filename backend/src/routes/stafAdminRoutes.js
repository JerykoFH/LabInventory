const express = require('express');
const router = express.Router();
const { protect, authorize } = require('../middleware/authMiddleware');
const {
    getLockedDrafts,
    getLockedDraftDetail,
    getAllAssets,
    getAssetById,
    updateAssetLabel,
    setReceivedDate,
    getAssetByCode,
    createAsset
} = require('../controllers/inventoryController');

router.use(protect, authorize('staf_admin'));

router.get('/procurements', getLockedDrafts);
router.get('/procurements/:id', getLockedDraftDetail);
router.get('/assets', getAllAssets);
router.get('/assets/scan/:code', getAssetByCode);
router.get('/assets/:id', getAssetById);
router.post('/assets', createAsset);
router.patch('/assets/:id/label', updateAssetLabel);
// router.patch('/assets/:id/receive', setReceivedDate); // DEPRECATED - Tanggal penerimaan diatur otomatis oleh sistem

module.exports = router;
