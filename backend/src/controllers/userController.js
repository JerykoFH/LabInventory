const User = require('../models/User');
const ProcurementDraft = require('../models/ProcurementDraft');
const MaintenanceLog = require('../models/MaintenanceLog');

/**
 * GET /api/admin/users
 * Lihat semua user
 */
const getAllUsers = async (req, res) => {
    try {
        const users = await User.find().select('-password').sort({ createdAt: -1 });
        res.json({ success: true, count: users.length, data: users });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * POST /api/admin/users
 * Buat user baru
 */
const createUser = async (req, res) => {
    try {
        const { name, email, password, role } = req.body;
        const user = await User.create({ name, email, password, role });
        res.status(201).json({
            success: true,
            data: { id: user._id, name: user.name, email: user.email, role: user.role },
        });
    } catch (error) {
        if (error.code === 11000) {
            return res.status(400).json({ success: false, message: 'Email already exists' });
        }
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/admin/users/:id
 * Lihat detail user
 */
const getUserById = async (req, res) => {
    try {
        const user = await User.findById(req.params.id).select('-password');
        if (!user) return res.status(404).json({ success: false, message: 'User not found' });
        res.json({ success: true, data: user });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PUT /api/admin/users/:id
 * Update user
 */
const updateUser = async (req, res) => {
    try {
        const { name, email, role, isActive, password } = req.body;
        const user = await User.findById(req.params.id);
        
        if (!user) return res.status(404).json({ success: false, message: 'User not found' });

        user.name = name || user.name;
        user.email = email || user.email;
        user.role = role || user.role;
        if (isActive !== undefined) {
            if (req.user && req.user._id.toString() === req.params.id && (isActive === false || isActive === 'false' || isActive === 0 || isActive === '0')) {
                return res.status(400).json({ success: false, message: 'Anda tidak dapat menonaktifkan akun Anda sendiri.' });
            }
            user.isActive = isActive;
        }
        if (password) {
            user.password = password;
        }

        await user.save();
        
        res.json({ success: true, data: { _id: user._id, name: user.name, email: user.email, role: user.role, isActive: user.isActive } });
    } catch (error) {
        if (error.code === 11000) {
            return res.status(400).json({ success: false, message: 'Email already exists' });
        }
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * DELETE /api/admin/users/:id
 * Hapus user jika tidak ada data terkait, atau nonaktifkan jika ada
 */
const deleteUser = async (req, res) => {
    try {
        const userId = req.params.id;

        // Cegah user menghapus akunnya sendiri
        if (req.user && req.user._id.toString() === userId) {
            return res.status(400).json({
                success: false,
                message: 'Anda tidak dapat menghapus akun Anda sendiri.'
            });
        }

        // Cek keterikatan data
        const hasDrafts = await ProcurementDraft.exists({ $or: [{ createdBy: userId }, { reviewedBy: userId }] });
        const hasLogs = await MaintenanceLog.exists({ performedBy: userId });

        if (hasDrafts || hasLogs) {
            // Soft delete
            const user = await User.findByIdAndUpdate(userId, { isActive: false }, { new: true });
            if (!user) return res.status(404).json({ success: false, message: 'User not found' });
            
            return res.json({ 
                success: true, 
                message: 'User dinonaktifkan karena memiliki data yang terikat.' 
            });
        }

        // Hard delete
        const user = await User.findByIdAndDelete(userId);
        if (!user) return res.status(404).json({ success: false, message: 'User not found' });
        
        res.json({ success: true, message: 'User berhasil dihapus permanen.' });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

module.exports = { getAllUsers, createUser, getUserById, updateUser, deleteUser };
