const mongoose = require('mongoose');

// Catatan setiap kali staf lab melakukan pemeliharaan pada suatu aset
const maintenanceLogSchema = new mongoose.Schema({
    // Ruangan yang di-maintain
    room: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Room',
        required: true,
    },
    // Asset yang dipelihara (bisa multiple)
    assets: [
        {
            asset: {
                type: mongoose.Schema.Types.ObjectId,
                ref: 'Asset',
                required: true,
            },
            conditionBefore: {
                type: String,
                enum: ['baik', 'rusak_ringan', 'rusak_berat', ''],
            },
            conditionAfter: {
                type: String,
                enum: ['baik', 'rusak_ringan', 'rusak_berat', 'tidak_aktif', ''],
            },
            // Foto kondisi sebelum dan sesudah maintenance
            conditionPhotoBefore: {
                type: String,
                trim: true,
            },
            conditionPhotoAfter: {
                type: String,
                trim: true,
            },
        }
    ],
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
    // Daftar barang habis pakai yang terpakai selama pemeliharaan
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
