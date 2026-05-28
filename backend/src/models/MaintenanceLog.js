const mongoose = require('mongoose');

// Catatan setiap kali ada pemeliharaan aset, diisi oleh Staf Lab
const maintenanceLogSchema = new mongoose.Schema({
    asset: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Asset',
        required: true,
    },
    performedBy: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User',
        required: true,
    },
    maintenanceDate: {
        type: Date,
        required: true,
        default: Date.now,
    },
    type: {
        type: String,
        enum: ['rutin', 'perbaikan', 'pengecekan'],
        default: 'rutin',
    },
    description: {
        type: String,
        required: [true, 'Maintenance description is required'],
        trim: true,
    },
    conditionBefore: {
        type: String,
        enum: ['baik', 'rusak_ringan', 'rusak_berat'],
    },
    conditionAfter: {
        type: String,
        enum: ['baik', 'rusak_ringan', 'rusak_berat', 'tidak_aktif'],
    },
    // BHP yang terpakai waktu maintenance berlangsung
    consumablesUsed: [
        {
            item: {
                type: mongoose.Schema.Types.ObjectId,
                ref: 'ConsumableItem',
            },
            quantityUsed: {
                type: Number,
                min: 0,
            },
        }
    ],
    notes: {
        type: String,
        trim: true,
    },
}, { timestamps: true });

module.exports = mongoose.model('MaintenanceLog', maintenanceLogSchema);
