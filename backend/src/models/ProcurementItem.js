const mongoose = require('mongoose');

// Tiap baris item yang ada dalam sebuah draf pengadaan
const procurementItemSchema = new mongoose.Schema({
    draft: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'ProcurementDraft',
        required: true,
    },
    itemType: {
        type: String,
        enum: ['asset', 'consumable'],
        required: true,
    },
    name: {
        type: String,
        required: true,
        trim: true,
    },
    quantity: {
        type: Number,
        required: true,
        min: [1, 'Quantity must be at least 1'],
    },
    unit: {
        type: String,
        trim: true,
    },
    estimatedPrice: {
        type: Number,
        required: true,
        min: [0, 'Price cannot be negative'],
    },
    purchaseLink: {
        type: String,
        trim: true,
    },
    // Aset lama yang mau diganti (opsional, khusus itemType='asset')
    replacedAsset: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Asset',
    },
    // Hasil review Kaprodi: pending, approved, atau rejected
    approvalStatus: {
        type: String,
        enum: ['pending', 'approved', 'rejected'],
        default: 'pending',
    },
    rejectionReason: {
        type: String,
        trim: true,
    },
    notes: {
        type: String,
        trim: true,
    },
}, { timestamps: true });

module.exports = mongoose.model('ProcurementItem', procurementItemSchema);
