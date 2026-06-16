const ActivityLog = require('../models/ActivityLog');

/**
 * GET /api/history
 * Mengambil riwayat aktivitas berdasarkan peran pengguna
 */
const getHistory = async (req, res) => {
    try {
        const user = req.user;
        let filter = {};

        // Filter berdasarkan role
        if (user.role === 'admin') {
            // Admin bisa melihat semuanya, tidak ada filter spesifik
        } else if (user.role === 'kepala_lab' || user.role === 'kaprodi') {
            // Kepala Lab & Kaprodi melihat log yang berhubungan dengan pengadaan
            filter.entityType = { $in: ['ProcurementDraft', 'ProcurementItem'] };
        } else if (user.role === 'staf_admin') {
            // Staf Admin melihat log inventaris dan penerimaan
            filter.entityType = { $in: ['Asset', 'ProcurementItem'] };
        } else if (user.role === 'staf_lab') {
            // Staf Lab melihat log BHP dan Maintenance
            filter.entityType = { $in: ['ConsumableItem', 'MaintenanceLog'] };
        }

        // Filter dari query parameters
        const { action, startDate, endDate } = req.query;

        if (action && action !== 'all') {
            filter.action = action;
        }

        if (startDate || endDate) {
            filter.createdAt = {};
            if (startDate) {
                filter.createdAt.$gte = new Date(startDate);
            }
            if (endDate) {
                // Tambahkan 1 hari (23:59:59) untuk endDate
                const end = new Date(endDate);
                end.setHours(23, 59, 59, 999);
                filter.createdAt.$lte = end;
            }
        }

        const logs = await ActivityLog.find(filter)
            .populate('user', 'name email role')
            .sort({ createdAt: -1 }) // Terbaru di atas
            .limit(100); // Batasi 100 log terakhir untuk performa

        res.json({ success: true, count: logs.length, data: logs });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

module.exports = {
    getHistory
};
