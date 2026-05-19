const ProcurementDraft = require('../models/ProcurementDraft');
const ProcurementItem = require('../models/ProcurementItem');

/**
 * GET /api/kaprodi/procurements
 * Lihat semua draf yang sudah di-submit
 */
const getSubmittedDrafts = async (req, res) => {
    try {
        const drafts = await ProcurementDraft.find({ status: { $in: ['submitted', 'locked'] } })
            .populate('createdBy', 'name email')
            .populate('reviewedBy', 'name email')
            .sort({ submittedAt: -1 });
        res.json({ success: true, count: drafts.length, data: drafts });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/kaprodi/procurements/:id
 * Lihat detail draf + items
 */
const getDraftDetail = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findOne({
            _id: req.params.id,
            status: { $in: ['submitted', 'locked'] },
        }).populate('createdBy', 'name email').populate('reviewedBy', 'name email');

        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });

        const items = await ProcurementItem.find({ draft: draft._id })
            .populate('replacedAsset', 'name assetCode');

        res.json({ success: true, data: { ...draft.toObject(), items } });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PATCH /api/kaprodi/procurements/:id/items/:itemId/review
 * Approve atau reject satu item
 * Body: { approvalStatus: 'approved'|'rejected', rejectionReason? }
 */
const reviewItem = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findById(req.params.id);
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });
        if (draft.status === 'locked') {
            return res.status(400).json({ success: false, message: 'Draft is locked and cannot be reviewed' });
        }

        const { approvalStatus, rejectionReason } = req.body;
        if (!['approved', 'rejected'].includes(approvalStatus)) {
            return res.status(400).json({ success: false, message: 'approvalStatus must be approved or rejected' });
        }

        const item = await ProcurementItem.findOneAndUpdate(
            { _id: req.params.itemId, draft: draft._id },
            { approvalStatus, rejectionReason: approvalStatus === 'rejected' ? rejectionReason : undefined },
            { new: true, runValidators: true }
        );
        if (!item) return res.status(404).json({ success: false, message: 'Item not found' });
        res.json({ success: true, data: item });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * POST /api/kaprodi/procurements/:id/finalize
 * Finalisasi draf → status menjadi 'locked'
 */
const finalizeDraft = async (req, res) => {
    try {
        const draft = await ProcurementDraft.findById(req.params.id);
        if (!draft) return res.status(404).json({ success: false, message: 'Draft not found' });
        if (draft.status !== 'submitted') {
            return res.status(400).json({ success: false, message: 'Only submitted drafts can be finalized' });
        }

        draft.status = 'locked';
        draft.lockedAt = new Date();
        draft.reviewedBy = req.user._id;
        await draft.save();

        res.json({ success: true, message: 'Draft finalized and locked', data: draft });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

module.exports = { getSubmittedDrafts, getDraftDetail, reviewItem, finalizeDraft };
