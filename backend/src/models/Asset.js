const mongoose = require('mongoose');

// Data inventaris fisik — komputer, proyektor, meja lab, dan sejenisnya
const assetSchema = new mongoose.Schema({
    name: {
        type: String,
        required: [true, 'Asset name is required'],
        trim: true,
    },
    assetCode: {
        type: String,
        unique: true,
        sparse: true,   // boleh kosong dulu sebelum staf admin kasih label
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
        type: String,   // path atau URL foto label/QR
    },
    qrCode: {
        type: String,   // string kode QR atau barcode
    },
    receivedDate: {
        type: Date,     // kapan barangnya sampai, diisi staf admin
    },
    // Dari item pengadaan mana aset ini berasal (kalau ada)
    procurementItem: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'ProcurementItem',
    },
    // Aset lama yang digantikan oleh aset ini
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
