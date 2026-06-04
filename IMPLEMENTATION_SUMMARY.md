# Implementasi Fitur Manajemen Inventaris: Staff Admin & Staff Lab

## Ringkasan Perubahan

Implementasi sistem manajemen inventaris laboratorium untuk dua role pengguna: Staf Administrasi dan Staf Laboratorium. Fitur mencakup pengelolaan penerimaan barang, pemeliharaan aset, dan manajemen stok barang habis pakai.

## Backend API Routes

### Staf Administrasi (Staff Admin)

**Endpoint**: `/api/staf-admin/*`

| Method | Endpoint | Fungsi | Perubahan |
|--------|----------|--------|----------|
| GET | `/procurements` | Tampilkan daftar pengadaan yang sudah final | - |
| GET | `/procurements/:id` | Lihat detail pengadaan dan item yang disetujui | - |
| GET | `/assets` | Ambil daftar semua barang inventaris | - |
| PATCH | `/assets/:id/label` | Update kode aset, foto label, atau QR code | - |
| PATCH | `/assets/:id/receive` | Catat tanggal penerimaan barang | Baru: Validasi untuk mencegah perubahan tanggal setelah barang diterima |

### Staf Laboratorium (Staff Lab)

**Endpoint**: `/api/staf-lab/*`

#### Barang Habis Pakai (BHP)
| Method | Endpoint | Fungsi | Perubahan |
|--------|----------|--------|----------|
| GET | `/consumables` | Daftar semua barang habis pakai dengan stok terkini | - |
| POST | `/consumables` | Daftarkan barang habis pakai baru | Baru: currentStock menjadi opsional (default 0) |
| PATCH | `/consumables/:id/stock` | Sesuaikan stok barang (tambah atau kurangi) | - |

#### Ruangan (Rooms)
| Method | Endpoint | Fungsi | Perubahan |
|--------|----------|--------|----------|
| GET | `/rooms` | Ambil daftar ruangan untuk dropdown form | Baru: Endpoint baru untuk support maintenance form |

#### Pemeliharaan Aset (Maintenance)
| Method | Endpoint | Fungsi | Perubahan |
|--------|----------|--------|----------|
| GET | `/maintenance` | Lihat semua catatan pemeliharaan aset | Baru: Populate room dan asset data |
| POST | `/maintenance` | Catat pemeliharaan baru dan kurangi stok BHP | Baru: Mendukung parameter `room` untuk tracking lokasi |
| GET | `/maintenance/:id` | Lihat detail pemeliharaan spesifik | Baru: Populate room data |

## Frontend Laravel Routes

### Staf Administrasi

**Route Prefix**: `staf-admin`

```
GET    /staf-admin/procurements          → InventoryController@procurements
GET    /staf-admin/procurements/{id}     → InventoryController@procurementDetail
GET    /staf-admin/assets                → InventoryController@assets
PATCH  /staf-admin/assets/{id}/label     → InventoryController@updateLabel
PATCH  /staf-admin/assets/{id}/receive   → InventoryController@setReceived
```

**Fitur Utama:**
- Melihat daftar pengadaan yang sudah disetujui kaprodi
- Memberi label dan kode aset pada barang baru
- Mencatat tanggal penerimaan barang dengan validasi anti-perubahan

### Staf Laboratorium

**Route Prefix**: `staf-lab`

**Barang Habis Pakai (BHP):**
```
GET    /staf-lab/consumables             → ConsumableController@index
GET    /staf-lab/consumables/create      → ConsumableController@create
POST   /staf-lab/consumables             → ConsumableController@store
PATCH  /staf-lab/consumables/{id}/stock  → ConsumableController@adjustStock
```

**Pemeliharaan Aset:**
```
GET    /staf-lab/maintenance             → MaintenanceController@index
GET    /staf-lab/maintenance/create      → MaintenanceController@create
POST   /staf-lab/maintenance             → MaintenanceController@store
GET    /staf-lab/maintenance/{id}        → MaintenanceController@show
```

**Fitur Utama:**
- Mendaftarkan barang habis pakai baru tanpa memerlukan stok awal
- Menyesuaikan stok barang (menambah atau mengurangi)
- Mencatat pemeliharaan aset dengan tracking ruangan dan barang habis yang dipakai
- Otomatis mengurangi stok barang habis saat pemeliharaan dicatat
- Memperbarui kondisi aset setelah pemeliharaan

## Perubahan Model Database

### MaintenanceLog
- **Baru**: Field `room` (ObjectId, ref: 'Room') untuk melacak ruangan tempat pemeliharaan dilakukan

## Perubahan Controller

### Backend

**inventoryController.js - `setReceivedDate()`**
- Validasi: Cegah perubahan tanggal penerimaan jika barang sudah pernah diterima sebelumnya
- Response: Return pesan error dengan tanggal penerimaan saat ini jika sudah ada

**labController.js - `createConsumable()`**
- currentStock menjadi parameter opsional dengan default value 0
- Menghilangkan requirement untuk input stok awal saat membuat barang habis pakai

**labController.js - `getAllMaintenanceLogs()`, `createMaintenanceLog()`, `getMaintenanceLogById()`**
- Tambahan: Populate field `room` saat mengambil data pemeliharaan
- Mendukung parameter `room` dalam request body

### Frontend

**StafAdmin/InventoryController.php**
- Improved error handling untuk `setReceived()` method
- Menampilkan pesan error yang lebih informatif

**StafLab/ConsumableController.php**
- Ubah validasi `currentStock` dari `required` menjadi `nullable`
- Support untuk menambah barang tanpa input stok awal

**StafLab/MaintenanceController.php**
- Update `create()`: Tambahan fetch data ruangan dari API `/api/staf-lab/rooms`
- Update `store()`: Tambahan validasi parameter `room` dengan status `nullable`

## Fitur Keamanan

- Semua routes dilindungi dengan middleware `protect` (autentikasi) dan `authorize` (otorisasi berdasarkan role)
- Validasi tanggal penerimaan mencegah perubahan data yang tidak konsisten
- Validasi stok mencegah keadaan negatif pada barang habis pakai

## Perubahan Dokumentasi Code

- Humanisasi semua comments di 7 file untuk readability yang lebih baik
- Menggunakan bahasa yang lebih natural dan mudah dipahami
- Penambahan konteks dan penjelasan untuk logika bisnis

## Testing Notes

Fitur berikut perlu divalidasi:
1. Staf admin tidak dapat mengubah tanggal penerimaan barang yang sudah pernah diterima
2. Barang habis pakai dapat ditambahkan tanpa input stok awal (default 0)
3. Stok barang habis otomatis berkurang saat pemeliharaan dicatat
4. Ruangan dapat dipilih pada form pemeliharaan dengan dropdown search
5. Kondisi aset diperbarui berdasarkan hasil pemeliharaan
