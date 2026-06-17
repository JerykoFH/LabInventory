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
    createAsset,
    setProcurementProgress,
    receiveProcurementItem,
    updateAssetCondition
} = require('../controllers/inventoryController');

router.use(protect);

router.get('/procurements', authorize('staf_admin'), getLockedDrafts);
router.get('/procurements/:id', authorize('staf_admin'), getLockedDraftDetail);
router.patch('/procurements/:id/progress', authorize('staf_admin'), setProcurementProgress);
router.patch('/procurements/:id/items/:itemId/receive', authorize('staf_admin'), receiveProcurementItem);

router.get('/assets', authorize('staf_admin', 'staf_lab'), getAllAssets);
router.get('/assets/scan/:code', authorize('staf_admin', 'staf_lab'), getAssetByCode);
router.get('/assets/:id', authorize('staf_admin', 'staf_lab'), getAssetById);
router.post('/assets', authorize('staf_admin', 'staf_lab'), createAsset);
router.patch('/assets/:id/label', authorize('staf_admin', 'staf_lab'), updateAssetLabel);
router.patch('/assets/:id/receive', authorize('staf_admin', 'staf_lab'), setReceivedDate);
router.patch('/assets/:id/condition', authorize('staf_admin', 'staf_lab'), updateAssetCondition);

module.exports = router;
