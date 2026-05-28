const mongoose = require('mongoose');

// Data ruangan yang ada di kampus, dikelola sama Admin
const roomSchema = new mongoose.Schema({
    name: {
        type: String,
        required: [true, 'Room name is required'],
        trim: true,
    },
    code: {
        type: String,
        required: [true, 'Room code is required'],
        unique: true,
        uppercase: true,
        trim: true,
    },
    location: {
        type: String,
        trim: true,
    },
    capacity: {
        type: Number,
        default: 0,
    },
    description: {
        type: String,
        trim: true,
    },
    isActive: {
        type: Boolean,
        default: true,
    },
}, { timestamps: true });

module.exports = mongoose.model('Room', roomSchema);
