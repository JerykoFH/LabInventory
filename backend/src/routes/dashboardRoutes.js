const express = require('express');
const router = express.Router();
const { protect } = require('../middleware/authMiddleware');
const { getDashboardStats } = require('../controllers/dashboardController');

// Route ini bisa diakses oleh semua role asalkan sudah login (protect)
router.use(protect);

router.get('/stats', getDashboardStats);

module.exports = router;
