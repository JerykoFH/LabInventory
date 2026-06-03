# LabInventory - Sistem Inventaris Laboratorium

Proyek ini merupakan sistem manajemen inventaris laboratorium berbasis web. Arsitektur sistem ini terbagi menjadi dua bagian utama:
1. Backend: API berbasis Node.js (Express) dan database MongoDB.
2. Frontend: Antarmuka pengguna yang dibangun menggunakan Laravel (Blade).

## Persiapan Sistem
Sebelum menjalankan aplikasi, pastikan perangkat Anda sudah terinstal:
- Node.js (versi 16 atau yang lebih baru)
- PHP (versi 8.2 atau yang lebih baru)
- Composer (untuk mengelola dependensi PHP)
- MongoDB (pastikan service database sudah berjalan di port lokal 27017, atau Anda dapat menggunakan MongoDB Atlas)

---

## Panduan Menjalankan Aplikasi

Karena aplikasi ini terbagi menjadi dua bagian (backend dan frontend), Anda perlu menjalankan keduanya secara bersamaan di dua terminal (CMD atau Powershell) yang berbeda.

### Langkah 1: Menyiapkan Backend (Terminal 1)
Buka terminal pertama dan arahkan direktori ke dalam folder `backend`:
```bash
cd backend
```

1. Instalasi dependensi Node.js:
   ```bash
   npm install
   ```

2. Konfigurasi environment:
   Salin file `.env.example` dan ubah namanya menjadi `.env`. Pastikan service MongoDB Anda sudah berjalan agar database dapat terhubung.
   ```bash
   cp .env.example .env
   ```

3. Mengisi data awal (Seeding):
   Langkah ini wajib dilakukan saat pertama kali menjalankan aplikasi untuk membuat akun pengguna, ruangan, dan contoh aset ke dalam database.
   ```bash
   npm run seed
   ```

4. Menjalankan server backend:
   ```bash
   npm run dev
   ```
   Server backend sekarang berjalan di `http://localhost:3000`. Biarkan terminal ini tetap terbuka.

---

### Langkah 2: Menyiapkan Frontend (Terminal 2)
Buka terminal kedua dan arahkan direktori ke dalam folder `frontend`:
```bash
cd frontend
```

1. Instalasi dependensi PHP dan Node.js:
   ```bash
   composer install
   npm install
   ```
   *(Catatan: Perintah `composer install` di atas sudah secara otomatis mengunduh semua library yang dibutuhkan untuk fitur cetak laporan PDF dan Excel).*

2. Konfigurasi environment:
   Salin file `.env.example` menjadi `.env`.
   ```bash
   cp .env.example .env
   ```
   Pastikan baris berikut ada di dalam file `.env` frontend Anda agar sistem dapat berkomunikasi dengan backend:
   ```env
   API_BASE_URL="http://localhost:3000"
   ```

3. Membuat Application Key Laravel:
   ```bash
   php artisan key:generate
   ```

4. Melakukan kompilasi aset frontend (CSS dan JS):
   ```bash
   npm run build
   ```

5. Menjalankan server frontend:
   ```bash
   php artisan serve
   ```
   Server frontend sekarang berjalan di `http://127.0.0.1:8000`.

---

## Hak Akses Pengguna
Setelah kedua server berhasil dijalankan, Anda dapat membuka browser dan mengakses aplikasi melalui tautan:
http://127.0.0.1:8000

Berikut adalah daftar akun yang dapat Anda gunakan untuk masuk ke dalam sistem. Semua akun memiliki kata sandi bawaan yaitu: `password123`.

| Peran | Email |
|------|-------------|
| Admin | `admin@lab.ac.id` |
| Kepala Lab | `kepalalab@lab.ac.id` |
| Kaprodi | `kaprodi@lab.ac.id` |
| Staf Admin | `stafadmin@lab.ac.id` |
| Staf Lab | `staflab@lab.ac.id` |

---

## Fitur Utama Aplikasi
- Manajemen Aset dan Ruangan: Dikelola secara penuh oleh Staf Admin.
- Pemindai Barcode/QR Pintar: Memudahkan pencarian aset dan memungkinkan penambahan barang instan langsung menggunakan kamera perangkat.
- Barang Habis Pakai dan Riwayat Pemeliharaan: Dikelola oleh Staf Lab.
- Pengadaan Barang: Proses pengajuan draf oleh Kepala Lab yang kemudian direview dan disetujui oleh Kaprodi.
- Cetak Laporan: Pimpinan laboratorium dapat mengunduh daftar inventaris dalam bentuk dokumen PDF maupun Microsoft Excel.
