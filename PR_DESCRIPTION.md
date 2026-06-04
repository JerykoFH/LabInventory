# Pull Request: Implementasi Manajemen Inventaris untuk Staff Admin & Staff Lab

## Deskripsi

Implementasi lengkap fitur manajemen inventaris laboratorium yang mencakup:
- Pengelolaan penerimaan barang dan pelabelan aset untuk Staff Administrasi
- Manajemen stok barang habis pakai dan pencatatan pemeliharaan untuk Staff Laboratorium
- Tracking pemeliharaan berdasarkan ruangan dengan otomasi pengurangan stok

## Tipe Perubahan

- Backend: API endpoints baru dan modifikasi validasi
- Frontend: Controller dan routing untuk Staff Admin dan Staff Lab
- Database: Penambahan field room di MaintenanceLog model
- Documentation: Humanisasi comments dan dokumentasi fitur

## Perubahan Spesifik

### Backend (Node.js)

1. **inventoryController.js**
   - Modifikasi method `setReceivedDate()`: Tambah validasi untuk mencegah perubahan tanggal penerimaan setelah barang diterima
   - Improvement: Return informasi tanggal penerimaan saat ini jika ada konflik

2. **labController.js**
   - Modifikasi `createConsumable()`: Buat parameter currentStock opsional (default: 0)
   - Modifikasi `getAllMaintenanceLogs()`: Tambah populate field room
   - Modifikasi `createMaintenanceLog()`: Support parameter room dan perbarui kondisi aset otomatis
   - Modifikasi `getMaintenanceLogById()`: Tambah populate field room

3. **stafLabRoutes.js**
   - Tambah route: `GET /api/staf-lab/rooms` untuk mendapatkan daftar ruangan

4. **MaintenanceLog.js (Model)**
   - Tambah field: `room` (ObjectId, ref: 'Room') untuk melacak ruangan tempat pemeliharaan

### Frontend (Laravel)

1. **StafAdmin/InventoryController.php**
   - Modifikasi `setReceived()`: Improve error handling dengan pesan yang lebih informatif

2. **StafLab/ConsumableController.php**
   - Modifikasi `store()`: Ubah validasi currentStock dari required menjadi nullable

3. **StafLab/MaintenanceController.php**
   - Modifikasi `create()`: Fetch data ruangan dari API untuk dropdown selection
   - Modifikasi `store()`: Tambah support untuk parameter room dengan validasi nullable

### Dokumentasi

- Humanisasi 20+ comments di 7 file untuk readability yang lebih baik
- Update penjelasan method, endpoint, dan business logic
- Gunakan bahasa yang lebih natural dan mudah dipahami

## API Routes yang Diimplementasikan

### Staff Admin
```
GET    /api/staf-admin/procurements
GET    /api/staf-admin/procurements/:id
GET    /api/staf-admin/assets
PATCH  /api/staf-admin/assets/:id/label
PATCH  /api/staf-admin/assets/:id/receive [MODIFIED]
```

### Staff Lab
```
GET    /api/staf-lab/consumables
POST   /api/staf-lab/consumables [MODIFIED]
PATCH  /api/staf-lab/consumables/:id/stock
GET    /api/staf-lab/rooms [NEW]
GET    /api/staf-lab/maintenance [MODIFIED]
POST   /api/staf-lab/maintenance [MODIFIED]
GET    /api/staf-lab/maintenance/:id [MODIFIED]
```

## Fitur yang Diimplementasikan

### Staff Administrasi
- Melihat daftar pengadaan yang sudah disetujui kaprodi
- Memberikan label dan kode aset pada barang baru
- Mencatat tanggal penerimaan barang dengan proteksi data

### Staff Laboratorium
- Mendaftarkan barang habis pakai tanpa input stok awal (dimulai dari 0)
- Menyesuaikan stok barang (menambah atau mengurangi)
- Mencatat pemeliharaan aset dengan tracking ruangan
- Otomasi pengurangan stok barang habis saat pemeliharaan dicatat
- Memperbarui kondisi aset setelah pemeliharaan

## Validasi & Keamanan

- Semua routes dilindungi middleware autentikasi dan otorisasi role-based
- Validasi tanggal penerimaan mencegah data inconsistency
- Validasi stok mencegah nilai negatif pada barang habis pakai
- Validasi aset dan consumable item sebelum operasi

## Testing Checklist

- [ ] Staff Admin tidak dapat mengubah tanggal penerimaan yang sudah ada
- [ ] Barang habis pakai dapat ditambahkan dengan stok 0 secara default
- [ ] Dropdown ruangan berfungsi dan dapat di-search
- [ ] Stok barang habis otomatis berkurang saat maintenance dicatat
- [ ] Kondisi aset diperbarui berdasarkan hasil maintenance
- [ ] Semua validasi berfungsi dengan proper error messages
- [ ] Unauthorized access ditolak dengan tepat

## Impact

- Memfasilitasi workflow pengadaan barang dari staff admin
- Meningkatkan tracking pemeliharaan aset dengan informasi lokasi
- Menyederhanakan input barang habis pakai dengan default value
- Otomasi manajemen stok barang habis pakai

## Notes

- Database schema perlu diupdate dengan menjalankan migration atau seeding ulang untuk MaintenanceLog
- Frontend form maintenance perlu di-render dengan dropdown ruangan yang searchable
- API endpoint baru `/api/staf-lab/rooms` harus accessible hanya oleh staff lab
