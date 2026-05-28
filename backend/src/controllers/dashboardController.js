const User = require('../models/User');
const Room = require('../models/Room');
const Asset = require('../models/Asset');
const ConsumableItem = require('../models/ConsumableItem');
const ProcurementDraft = require('../models/ProcurementDraft');

/**
 * GET /api/dashboard/stats
 * Ambil semua statistik global untuk ditampilkan di dashboard (bisa diakses semua role)
 */
const getDashboardStats = async (req, res) => {
    try {
        const [
            totalUsers,
            totalRooms,
            totalAssets,
            totalConsumables,
            lowStockConsumables,
            totalDrafts,
            submittedDrafts,
            maintenanceNeeded
        ] = await Promise.all([
            User.countDocuments({ isActive: true }),
            Room.countDocuments(),
            Asset.countDocuments({ status: { $ne: 'dihapus' } }),
            ConsumableItem.countDocuments(),
            ConsumableItem.countDocuments({ $expr: { $lte: ['$currentStock', '$minimumStock'] } }),
            ProcurementDraft.countDocuments(),
            ProcurementDraft.countDocuments({ status: 'submitted' }),
            Asset.countDocuments({ status: 'dalam_pemeliharaan' })
        ]);

        res.json({
            success: true,
            data: {
                totalUsers,
                totalRooms,
                totalAssets,
                totalConsumables,
                lowStockConsumables,
                totalDrafts,
                submittedDrafts,
                maintenanceNeeded
            }
        });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

module.exports = { getDashboardStats };
