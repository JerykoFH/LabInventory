const mongoose = require('mongoose');

/**
 * ProcurementDraft — draf pengadaan tahunan dari Kepala Laboratorium
 * Status flow: draft → submitted → locked (setelah kaprodi finalisasi)
 */
const procurementDraftSchema = new mongoose.Schema({
    title: {
        type: String,
        required: [true, 'Draft title is required'],
        trim: true,
    },
    year: {
        type: Number,
        required: [true, 'Procurement year is required'],
    },
    createdBy: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User',
        required: true,
    },
    reviewedBy: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User',   // kaprodi yang mereview
    },
    status: {
        type: String,
        enum: ['draft', 'submitted', 'locked'],
        default: 'draft',
    },
    submittedAt: {
        type: Date,
    },
    lockedAt: {
        type: Date,
    },
    notes: {
        type: String,
        trim: true,
    },
}, { timestamps: true });

module.exports = mongoose.model('ProcurementDraft', procurementDraftSchema);
