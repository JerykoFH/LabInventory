const jwt = require('jsonwebtoken');
const User = require('../models/User');

/**
 * protect = ngecek JWT token dari header Authorization
 * kalau token valid, data user bakal disimpan ke req.user
 */
const protect = async (req, res, next) => {
    try {
        let token;

        // Ambil token dari header Authorization: Bearer <token>
        if (req.headers.authorization && req.headers.authorization.startsWith('Bearer')) {
            token = req.headers.authorization.split(' ')[1];
        }

        if (!token) {
            return res.status(401).json({
                success: false,
                message: 'Not authorized — no token provided',
            });
        }

        // Cek tokennya valid atau tidak
        const decoded = jwt.verify(token, process.env.JWT_SECRET);

        // Cari user dari id yang ada di token, password tidak ikut diambil
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

        // Simpan data user ke request biar bisa dipakai di controller berikutnya
        req.user = user;
        next();
    } catch (error) {
        // Kalau token bermasalah, error-nya ditangani di sini
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

/**
 * authorize = ngecek apakah role user boleh akses route ini atau tidak
 * dipakai setelah protect, contoh: authorize('admin', 'kepala_lab')
 * roles = daftar role yang boleh akses
 */
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
