const ActivityLog = require('../models/ActivityLog');

/**
 * Mencatat aktivitas pengguna ke database (Audit Log)
 * @param {Object} req - Express Request object (untuk mengambil data req.user)
 * @param {String} action - Jenis aksi (CREATE, UPDATE, DELETE, APPROVE, REJECT, ADJUST_STOCK, dll)
 * @param {String} entityType - Model/Entitas yang terpengaruh (Asset, ConsumableItem, dll)
 * @param {String|ObjectId} entityId - ID dari entitas yang terpengaruh
 * @param {String} description - Penjelasan singkat (readable format)
 * @param {Object} metadata - Data tambahan (old value, new value, dll)
 */
const logActivity = async (req, action, entityType, entityId, description, metadata = {}) => {
    try {
        if (!req || !req.user || !req.user._id) {
            console.warn('[Logger] Gagal mencatat log: User tidak ditemukan dalam request.');
            return;
        }

        const log = new ActivityLog({
            user: req.user._id,
            action,
            entityType,
            entityId: entityId || null,
            description,
            metadata
        });

        await log.save();
    } catch (error) {
        // Jangan hentikan eksekusi utama jika log gagal disimpan, cukup catat di console
        console.error('[Logger] Gagal menyimpan activity log:', error.message);
    }
};

module.exports = { logActivity };
