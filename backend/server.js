require('dotenv').config();
const express = require('express');
const cors = require('cors');
const connectDB = require('./src/config/db');

// Route imports
const authRoutes       = require('./src/routes/authRoutes');
const adminRoutes      = require('./src/routes/adminRoutes');
const kepalaLabRoutes  = require('./src/routes/kepalaLabRoutes');
const kaprodiRoutes    = require('./src/routes/kaprodiRoutes');
const stafAdminRoutes  = require('./src/routes/stafAdminRoutes');
const stafLabRoutes    = require('./src/routes/stafLabRoutes');

// Koneksi MongoDB
connectDB();

const app = express();

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Routes
app.use('/api/auth',       authRoutes);
app.use('/api/admin',      adminRoutes);
app.use('/api/kepala-lab', kepalaLabRoutes);
app.use('/api/kaprodi',    kaprodiRoutes);
app.use('/api/staf-admin', stafAdminRoutes);
app.use('/api/staf-lab',   stafLabRoutes);

// Health check
app.get('/api/health', (req, res) => {
    res.json({ success: true, message: 'LabInventory API is running', timestamp: new Date() });
});

// 404 handler
app.use((req, res) => {
    res.status(404).json({ success: false, message: `Route ${req.originalUrl} not found` });
});

// Global error handler
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ success: false, message: 'Internal Server Error', error: err.message });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`LabInventory API running on http://localhost:${PORT}`);
});

module.exports = app;
