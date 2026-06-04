const multer = require('multer');
const path = require('path');
const fs = require('fs');

// Ensure upload directory exists
const uploadDir = path.join(__dirname, '../../../frontend/public/uploads/maintenance');
if (!fs.existsSync(uploadDir)) {
    fs.mkdirSync(uploadDir, { recursive: true });
}

// Multer config
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        // Save to frontend public directory so it can be served directly by Laravel if needed
        // But since they might run on different servers, usually we save it in backend public dir
        // Wait, the plan said "backend public/uploads/maintenance". Let's use backend directory.
        const backendUploadDir = path.join(__dirname, '../../public/uploads/maintenance');
        if (!fs.existsSync(backendUploadDir)) {
            fs.mkdirSync(backendUploadDir, { recursive: true });
        }
        cb(null, backendUploadDir);
    },
    filename: (req, file, cb) => {
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        cb(null, 'maintenance-' + uniqueSuffix + path.extname(file.originalname));
    }
});

const fileFilter = (req, file, cb) => {
    if (file.mimetype === 'image/jpeg' || file.mimetype === 'image/png' || file.mimetype === 'image/webp') {
        cb(null, true);
    } else {
        cb(new Error('Format file tidak didukung. Hanya JPG, PNG, WEBP yang diperbolehkan.'), false);
    }
};

const upload = multer({ 
    storage: storage,
    fileFilter: fileFilter,
    limits: {
        fileSize: 2 * 1024 * 1024 // 2MB max
    }
});

module.exports = upload;
