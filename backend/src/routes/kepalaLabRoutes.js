const express = require('express');
const router = express.Router();
const { protect, authorize } = require('../middleware/authMiddleware');
const {
    getMyDrafts, createDraft, getDraftById, updateDraft, submitDraft,
    addItem, updateItem, deleteItem,
} = require('../controllers/procurementController');

router.use(protect, authorize('kepala_lab'));

// Draf pengadaan
router.get('/', getMyDrafts);
router.post('/', createDraft);
router.get('/:id', getDraftById);
router.put('/:id', updateDraft);
router.post('/:id/submit', submitDraft);

// Items dalam draf
router.post('/:id/items', addItem);
router.put('/:id/items/:itemId', updateItem);
router.delete('/:id/items/:itemId', deleteItem);

module.exports = router;
