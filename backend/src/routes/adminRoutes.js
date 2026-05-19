const express = require('express');
const router = express.Router();
const { protect, authorize } = require('../middleware/authMiddleware');

const {
    getAllUsers, createUser, getUserById, updateUser, deleteUser,
} = require('../controllers/userController');

const {
    getAllRooms, createRoom, getRoomById, updateRoom, deleteRoom,
} = require('../controllers/roomController');

// Semua route di sini hanya untuk role 'admin'
router.use(protect, authorize('admin'));

// Users
router.get('/users', getAllUsers);
router.post('/users', createUser);
router.get('/users/:id', getUserById);
router.put('/users/:id', updateUser);
router.delete('/users/:id', deleteUser);

// Rooms
router.get('/rooms', getAllRooms);
router.post('/rooms', createRoom);
router.get('/rooms/:id', getRoomById);
router.put('/rooms/:id', updateRoom);
router.delete('/rooms/:id', deleteRoom);

module.exports = router;
