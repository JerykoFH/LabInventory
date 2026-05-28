const mongoose = require('mongoose');

// Barang yang bisa habis (consumed)
const consumableItemSchema = new mongoose.Schema({
    name: {
        type: String,
        required: [true, 'Consumable item name is required'],
        trim: true,
    },
    category: {
        type: String,
        trim: true,
    },
    unit: {
        type: String,   
        required: true,
        trim: true,
    },
    currentStock: {
        type: Number,
        required: true,
        default: 0,
        min: [0, 'Stock cannot be negative'],
    },
    minimumStock: {
        type: Number,
        default: 5,     
    },
    location: {
        type: String,
        trim: true,
    },
    notes: {
        type: String,
        trim: true,
    },
}, { timestamps: true });

module.exports = mongoose.model('ConsumableItem', consumableItemSchema);
