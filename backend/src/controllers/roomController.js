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
        const room = await Room.create(req.body);
        res.status(201).json({ success: true, data: room });
    } catch (error) {
        if (error.code === 11000) {
            return res.status(400).json({ success: false, message: 'Room code already exists' });
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
        const room = await Room.findByIdAndUpdate(req.params.id, req.body, {
            new: true, runValidators: true,
        });
        if (!room) return res.status(404).json({ success: false, message: 'Room not found' });
        res.json({ success: true, data: room });
    } catch (error) {
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
