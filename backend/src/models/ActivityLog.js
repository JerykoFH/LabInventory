const mongoose = require('mongoose');

const activityLogSchema = new mongoose.Schema({
    user: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User',
        required: true
    },
    action: {
        type: String,
        required: true,
        enum: ['CREATE', 'UPDATE', 'DELETE', 'APPROVE', 'REJECT', 'ADJUST_STOCK', 'LOGIN', 'OTHER']
    },
    entityType: {
        type: String,
        required: true,
        enum: ['Asset', 'ConsumableItem', 'ProcurementDraft', 'ProcurementItem', 'MaintenanceLog', 'Room', 'User', 'System']
    },
    entityId: {
        type: mongoose.Schema.Types.ObjectId,
        required: false // Bisa kosong jika action bersifat global (misal System)
    },
    description: {
        type: String,
        required: true
    },
    metadata: {
        type: Object, // Untuk menyimpan data lama/baru atau info tambahan
        default: {}
    }
}, {
    timestamps: true
});

module.exports = mongoose.model('ActivityLog', activityLogSchema);
