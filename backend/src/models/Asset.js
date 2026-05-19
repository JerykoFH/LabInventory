const mongoose = require('mongoose');

/**
 * Asset (Inventaris) Schema
 * Contoh: komputer, proyektor, meja lab, dll.
 */
const assetSchema = new mongoose.Schema({
    name: {
        type: String,
        required: [true, 'Asset name is required'],
        trim: true,
    },
    assetCode: {
        type: String,
        unique: true,
        sparse: true,   // bisa null sebelum diberi label oleh staf admin
        trim: true,
    },
    category: {
        type: String,
        trim: true,
    },
    room: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Room',
    },
    condition: {
        type: String,
        enum: ['baik', 'rusak_ringan', 'rusak_berat', 'tidak_aktif'],
        default: 'baik',
    },
    status: {
        type: String,
        enum: ['aktif', 'dalam_pemeliharaan', 'dihapus', 'diganti'],
        default: 'aktif',
    },
    purchaseDate: {
        type: Date,
    },
    purchasePrice: {
        type: Number,
        default: 0,
    },
    labelPhoto: {
        type: String,   // path/URL foto QR/Barcode
    },
    qrCode: {
        type: String,   // kode QR/barcode string
    },
    receivedDate: {
        type: Date,     // tanggal penerimaan barang (diisi staf admin)
    },
    // Referensi ke item pengadaan asal (opsional)
    procurementItem: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'ProcurementItem',
    },
    // Aset yang digantikan oleh aset ini
    replacedAsset: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Asset',
    },
    notes: {
        type: String,
        trim: true,
    },
}, { timestamps: true });

module.exports = mongoose.model('Asset', assetSchema);
