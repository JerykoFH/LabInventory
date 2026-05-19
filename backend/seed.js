/**
 * Jalankan: node seed.js
 *
 * Data yang dibuat:
 * - 5 user (1 per role)
 * - 4 ruangan lab
 * - 6 aset inventaris
 * - 5 BHP (Barang Habis Pakai)
 * - 1 draf pengadaan contoh + 3 item
 */

require('dotenv').config();
const mongoose = require('mongoose');
const bcrypt = require('bcryptjs');

const User = require('./src/models/User');
const Room = require('./src/models/Room');
const Asset = require('./src/models/Asset');
const ConsumableItem = require('./src/models/ConsumableItem');
const ProcurementDraft = require('./src/models/ProcurementDraft');
const ProcurementItem = require('./src/models/ProcurementItem');

const MONGO_URI = process.env.MONGO_URI || 'mongodb://localhost:27017/lab_inventory';

async function seed() {
    try {
        await mongoose.connect(MONGO_URI);
        console.log('MongoDB Connected for seeding...');

        // Hapus data lama
        await Promise.all([
            User.deleteMany({}),
            Room.deleteMany({}),
            Asset.deleteMany({}),
            ConsumableItem.deleteMany({}),
            ProcurementDraft.deleteMany({}),
            ProcurementItem.deleteMany({}),
        ]);
        console.log('Old data cleared.');

        // 1. Users
        const hashedPassword = await bcrypt.hash('password123', 12);

        const users = await User.insertMany([
            {
                name: 'Admin Utama',
                email: 'admin@lab.ac.id',
                password: hashedPassword,
                role: 'admin',
                isActive: true,
            },
            {
                name: 'Dr. Budi Santoso',
                email: 'kepalalab@lab.ac.id',
                password: hashedPassword,
                role: 'kepala_lab',
                isActive: true,
            },
            {
                name: 'Prof. Siti Aminah',
                email: 'kaprodi@lab.ac.id',
                password: hashedPassword,
                role: 'kaprodi',
                isActive: true,
            },
            {
                name: 'Rina Wulandari',
                email: 'stafadmin@lab.ac.id',
                password: hashedPassword,
                role: 'staf_admin',
                isActive: true,
            },
            {
                name: 'Agus Prasetyo',
                email: 'staflab@lab.ac.id',
                password: hashedPassword,
                role: 'staf_lab',
                isActive: true,
            },
        ]);

        console.log(`${users.length} users created`);

        // Simpan referensi user
        const adminUser = users[0];
        const kepalaLab = users[1];
        const kaprodi = users[2];
        const stafAdmin = users[3];
        const stafLab = users[4];

        // 2. Rooms
        const rooms = await Room.insertMany([
            {
                name: 'Laboratorium Jaringan Komputer',
                code: 'LAB-JK-01',
                location: 'Gedung A, Lantai 2',
                capacity: 40,
                description: 'Lab untuk praktikum jaringan komputer dan keamanan siber',
            },
            {
                name: 'Laboratorium Pemrograman',
                code: 'LAB-PM-01',
                location: 'Gedung A, Lantai 3',
                capacity: 35,
                description: 'Lab untuk praktikum pemrograman dasar dan lanjut',
            },
            {
                name: 'Laboratorium Basis Data',
                code: 'LAB-BD-01',
                location: 'Gedung B, Lantai 1',
                capacity: 30,
                description: 'Lab untuk praktikum basis data dan data mining',
            },
            {
                name: 'Laboratorium Multimedia',
                code: 'LAB-MM-01',
                location: 'Gedung B, Lantai 2',
                capacity: 25,
                description: 'Lab untuk praktikum desain grafis dan multimedia',
            },
        ]);

        console.log(`${rooms.length} rooms created`);

        // 3. Assets (Inventaris) 
        const assets = await Asset.insertMany([
            {
                name: 'Komputer Desktop HP ProDesk 400',
                assetCode: 'INV-JK-001',
                category: 'Komputer',
                room: rooms[0]._id,
                condition: 'baik',
                status: 'aktif',
                purchaseDate: new Date('2024-03-15'),
                purchasePrice: 12000000,
                receivedDate: new Date('2024-04-01'),
            },
            {
                name: 'Komputer Desktop HP ProDesk 400',
                assetCode: 'INV-JK-002',
                category: 'Komputer',
                room: rooms[0]._id,
                condition: 'baik',
                status: 'aktif',
                purchaseDate: new Date('2024-03-15'),
                purchasePrice: 12000000,
                receivedDate: new Date('2024-04-01'),
            },
            {
                name: 'Monitor LG 24 inch IPS',
                assetCode: 'INV-JK-003',
                category: 'Monitor',
                room: rooms[0]._id,
                condition: 'baik',
                status: 'aktif',
                purchaseDate: new Date('2024-03-15'),
                purchasePrice: 2500000,
                receivedDate: new Date('2024-04-01'),
            },
            {
                name: 'Proyektor Epson EB-X51',
                assetCode: 'INV-PM-001',
                category: 'Proyektor',
                room: rooms[1]._id,
                condition: 'rusak_ringan',
                status: 'dalam_pemeliharaan',
                purchaseDate: new Date('2022-08-10'),
                purchasePrice: 7500000,
                receivedDate: new Date('2022-09-01'),
                notes: 'Lampu proyektor mulai redup, perlu penggantian',
            },
            {
                name: 'Switch Cisco Catalyst 2960',
                assetCode: 'INV-JK-004',
                category: 'Jaringan',
                room: rooms[0]._id,
                condition: 'baik',
                status: 'aktif',
                purchaseDate: new Date('2023-06-20'),
                purchasePrice: 15000000,
                receivedDate: new Date('2023-07-10'),
            },
            {
                name: 'Printer HP LaserJet Pro M404dn',
                assetCode: 'INV-BD-001',
                category: 'Printer',
                room: rooms[2]._id,
                condition: 'rusak_berat',
                status: 'dihapus',
                purchaseDate: new Date('2021-01-15'),
                purchasePrice: 4500000,
                receivedDate: new Date('2021-02-01'),
                notes: 'Sudah tidak bisa digunakan, perlu diganti',
            },
        ]);

        console.log(`${assets.length} assets created`);

        // 4. Consumable Items (BHP) 
        const consumables = await ConsumableItem.insertMany([
            {
                name: 'Kabel UTP Cat6 (1 box = 305m)',
                category: 'Kabel',
                unit: 'box',
                currentStock: 3,
                minimumStock: 2,
                location: 'Gudang Lab Jaringan',
            },
            {
                name: 'Konektor RJ-45',
                category: 'Konektor',
                unit: 'pack (100 pcs)',
                currentStock: 10,
                minimumStock: 5,
                location: 'Gudang Lab Jaringan',
            },
            {
                name: 'Toner HP 76A (CF276A)',
                category: 'Toner',
                unit: 'pcs',
                currentStock: 1,
                minimumStock: 2,
                location: 'Gudang Lab Basis Data',
                notes: 'Stok menipis, perlu pengadaan segera',
            },
            {
                name: 'Thermal Paste Arctic MX-4',
                category: 'Perawatan',
                unit: 'tube',
                currentStock: 8,
                minimumStock: 3,
                location: 'Gudang Lab Pemrograman',
            },
            {
                name: 'Tisu Pembersih LCD',
                category: 'Perawatan',
                unit: 'pack (50 lembar)',
                currentStock: 12,
                minimumStock: 5,
                location: 'Gudang Lab Multimedia',
            },
        ]);

        console.log(`${consumables.length} consumable items created`);

        // 5. Contoh Procurement Draft 
        const draft = await ProcurementDraft.create({
            title: 'Pengadaan Peralatan Lab Jaringan 2026',
            year: 2026,
            createdBy: kepalaLab._id,
            status: 'submitted',
            submittedAt: new Date('2026-05-10'),
            notes: 'Pengadaan tahunan untuk upgrade peralatan lab jaringan',
        });

        const procItems = await ProcurementItem.insertMany([
            {
                draft: draft._id,
                itemType: 'asset',
                name: 'Komputer Desktop Dell OptiPlex 7010',
                quantity: 5,
                unit: 'unit',
                estimatedPrice: 14000000,
                purchaseLink: 'https://www.tokopedia.com/dell-optiplex-7010',
                approvalStatus: 'approved',
                notes: 'Untuk mengganti komputer lama di Lab Jaringan',
            },
            {
                draft: draft._id,
                itemType: 'asset',
                name: 'Proyektor Epson EB-W52',
                quantity: 1,
                unit: 'unit',
                estimatedPrice: 8500000,
                purchaseLink: 'https://www.tokopedia.com/epson-eb-w52',
                replacedAsset: assets[3]._id,  
                approvalStatus: 'pending',
                notes: 'Pengganti proyektor EB-X51 yang sudah rusak ringan',
            },
            {
                draft: draft._id,
                itemType: 'consumable',
                name: 'Kabel UTP Cat6 (1 box)',
                quantity: 5,
                unit: 'box',
                estimatedPrice: 850000,
                purchaseLink: 'https://www.tokopedia.com/kabel-utp-cat6',
                approvalStatus: 'rejected',
                rejectionReason: 'Stok masih cukup untuk semester ini',
            },
        ]);

        console.log(`1 procurement draft + ${procItems.length} items created`);
        console.log('  Seeding selesai!');
        console.log('\nAkun login (password semua: password123):');
        console.log('  Admin        → admin@lab.ac.id');
        console.log('  Kepala Lab   → kepalalab@lab.ac.id');
        console.log('  Kaprodi      → kaprodi@lab.ac.id');
        console.log('  Staf Admin   → stafadmin@lab.ac.id');
        console.log('  Staf Lab     → staflab@lab.ac.id');
        process.exit(0);
    } catch (error) {
        console.error('Seeding error:', error.message);
        process.exit(1);
    }
}

seed();
