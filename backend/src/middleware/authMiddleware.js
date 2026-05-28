const jwt = require('jsonwebtoken');
const User = require('../models/User');

// Cek JWT token dari header Authorization, kalau valid simpan user ke req.user
const protect = async (req, res, next) => {
    try {
        let token;

        // Ambil token dari Authorization: Bearer <token>
        if (req.headers.authorization && req.headers.authorization.startsWith('Bearer')) {
            token = req.headers.authorization.split(' ')[1];
        }

        if (!token) {
            return res.status(401).json({
                success: false,
                message: 'Not authorized — no token provided',
            });
        }

        // Verifikasi token ke JWT_SECRET
        const decoded = jwt.verify(token, process.env.JWT_SECRET);

        // Cari usernya di DB pakai ID dari token, password jangan ikut
        const user = await User.findById(decoded.id).select('-password');

        if (!user) {
            return res.status(401).json({
                success: false,
                message: 'Not authorized — user no longer exists',
            });
        }

        if (!user.isActive) {
            return res.status(403).json({
                success: false,
                message: 'Not authorized — account is deactivated',
            });
        }

        // Tempel data user ke request biar controller bisa pakai
        req.user = user;
        next();
    } catch (error) {
        // Tangkap error JWT yang mungkin muncul
        if (error.name === 'JsonWebTokenError') {
            return res.status(401).json({
                success: false,
                message: 'Not authorized — invalid token',
            });
        }
        if (error.name === 'TokenExpiredError') {
            return res.status(401).json({
                success: false,
                message: 'Not authorized — token expired',
            });
        }

        return res.status(500).json({
            success: false,
            message: error.message,
        });
    }
};

// Cek apakah role user termasuk yang boleh akses route ini
// Pakai setelah protect, contoh: authorize('admin', 'kepala_lab')
const authorize = (...roles) => {
    return (req, res, next) => {
        if (!req.user) {
            return res.status(401).json({
                success: false,
                message: 'Not authorized — please login first',
            });
        }

        if (!roles.includes(req.user.role)) {
            return res.status(403).json({
                success: false,
                message: `Role '${req.user.role}' is not authorized to access this resource`,
            });
        }

        next();
    };
};

module.exports = { protect, authorize };
