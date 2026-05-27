const express = require('express');
const router = express.Router();
const { protect, authorize } = require('../middleware/authMiddleware');
const {
    getMyDrafts, createDraft, getDraftById, updateDraft, submitDraft, deleteDraft,
    addItem, updateItem, deleteItem,
} = require('../controllers/procurementController');

router.use(protect, authorize('kepala_lab'));

// Draf pengadaan
router.get('/procurements', getMyDrafts);
router.post('/procurements', createDraft);
router.get('/procurements/:id', getDraftById);
router.put('/procurements/:id', updateDraft);
router.delete('/procurements/:id', deleteDraft);
router.post('/procurements/:id/submit', submitDraft);

// Items dalam draf
router.post('/procurements/:id/items', addItem);
router.put('/procurements/:id/items/:itemId', updateItem);
router.delete('/procurements/:id/items/:itemId', deleteItem);

module.exports = router;
