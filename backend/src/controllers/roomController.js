const Room = require('../models/Room');

/**
 * GET /api/admin/rooms
 */
const getAllRooms = async (req, res) => {
    try {
        const rooms = await Room.find({ isActive: true }).sort({ code: 1 });
        res.json({ success: true, count: rooms.length, data: rooms });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * POST /api/admin/rooms
 */
const createRoom = async (req, res) => {
    try {
        // Cek duplikat nama (case-insensitive)
        if (req.body.name) {
            const existingName = await Room.findOne({ name: { $regex: new RegExp(`^${req.body.name}$`, 'i') } });
            if (existingName) {
                return res.status(400).json({ success: false, message: 'Nama ruangan sudah digunakan' });
            }
        }

        const room = await Room.create(req.body);
        res.status(201).json({ success: true, data: room });
    } catch (error) {
        if (error.code === 11000) {
            const field = Object.keys(error.keyPattern)[0];
            const msg = field === 'name'
                ? 'Nama ruangan sudah digunakan'
                : 'Kode ruangan sudah digunakan';
            return res.status(400).json({ success: false, message: msg });
        }
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * GET /api/admin/rooms/:id
 */
const getRoomById = async (req, res) => {
    try {
        const room = await Room.findById(req.params.id);
        if (!room) return res.status(404).json({ success: false, message: 'Room not found' });
        res.json({ success: true, data: room });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * PUT /api/admin/rooms/:id
 */
const updateRoom = async (req, res) => {
    try {
        // Cek duplikat nama (case-insensitive)
        if (req.body.name) {
            const existingName = await Room.findOne({ name: { $regex: new RegExp(`^${req.body.name}$`, 'i') } });
            if (existingName && existingName._id.toString() !== req.params.id) {
                return res.status(400).json({ success: false, message: 'Nama ruangan sudah digunakan' });
            }
        }

        const room = await Room.findByIdAndUpdate(req.params.id, req.body, {
            new: true, runValidators: true,
        });
        if (!room) return res.status(404).json({ success: false, message: 'Room not found' });
        res.json({ success: true, data: room });
    } catch (error) {
        if (error.code === 11000) {
            const field = Object.keys(error.keyPattern)[0];
            const msg = field === 'name'
                ? 'Nama ruangan sudah digunakan'
                : 'Kode ruangan sudah digunakan';
            return res.status(400).json({ success: false, message: msg });
        }
        res.status(500).json({ success: false, message: error.message });
    }
};

/**
 * DELETE /api/admin/rooms/:id  (soft delete)
 */
const deleteRoom = async (req, res) => {
    try {
        const room = await Room.findByIdAndUpdate(req.params.id, { isActive: false }, { new: true });
        if (!room) return res.status(404).json({ success: false, message: 'Room not found' });
        res.json({ success: true, message: 'Room deactivated successfully' });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
};

module.exports = { getAllRooms, createRoom, getRoomById, updateRoom, deleteRoom };
