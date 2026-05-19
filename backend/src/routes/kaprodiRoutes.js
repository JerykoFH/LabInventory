const express = require('express');
const router = express.Router();
const { protect, authorize } = require('../middleware/authMiddleware');
const {
    getSubmittedDrafts, getDraftDetail, reviewItem, finalizeDraft,
} = require('../controllers/procurementReviewController');

router.use(protect, authorize('kaprodi'));

router.get('/procurements', getSubmittedDrafts);
router.get('/procurements/:id', getDraftDetail);
router.patch('/procurements/:id/items/:itemId/review', reviewItem);
router.post('/procurements/:id/finalize', finalizeDraft);

module.exports = router;
