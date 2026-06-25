<div align="center">

# 📦 Inventory System
### Sistem Manajemen Stok Bahan Baku Gudang

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)

Sistem manajemen stok berbasis web yang dilengkapi perhitungan
**Economic Order Quantity (EOQ)** dan prediksi permintaan menggunakan
**Weighted Moving Average (WMA)**.

[Demo](#) · [Laporan Bug](#) · [Fitur Baru](#)

</div>

---

## 📋 Daftar Isi

- [Tentang Sistem](#-tentang-sistem)
- [Fitur Utama](#-fitur-utama)
- [Teknologi](#-teknologi)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Akun Demo](#-akun-demo)
- [Struktur Folder](#-struktur-folder)
- [Metodologi](#-metodologi)
- [Screenshot](#-screenshot)
- [Lisensi](#-lisensi)

---

## 🏭 Tentang Sistem

**Inventory System** adalah aplikasi web untuk mengelola stok bahan baku
gudang secara efisien. Sistem ini dibangun sebagai penelitian skripsi yang
mengimplementasikan metode ilmiah dalam manajemen persediaan.

### Permasalahan yang Diselesaikan

| Masalah | Solusi Sistem |
|---------|---------------|
| Overstock (kelebihan stok) | Kalkulasi EOQ otomatis |
| Stockout (kehabisan stok) | Notifikasi & Reorder Point |
| Pengadaan tidak terencana | Rekomendasi pemesanan berbasis data |
| Prediksi permintaan manual | Weighted Moving Average otomatis |
| Laporan manual di Excel | Laporan otomatis PDF & Excel |

---

## ✨ Fitur Utama

### 🗄️ Master Data
- ✅ Manajemen barang dengan foto dan parameter EOQ
- ✅ Manajemen kategori barang
- ✅ Manajemen supplier
- ✅ Manajemen pengguna dengan role-based access

### 📊 Transaksi
- ✅ Input barang masuk dengan nomor dokumen otomatis
- ✅ Input barang keluar dengan validasi stok real-time
- ✅ Permintaan barang dengan workflow approval
- ✅ Pengadaan barang dengan multi-level approval

### 🔬 Analisis (Inti Skripsi)
- ✅ **Kalkulasi EOQ** — menentukan jumlah pemesanan optimal
- ✅ **Safety Stock** — stok pengaman berbasis permintaan
- ✅ **Reorder Point (ROP)** — kapan harus memesan ulang
- ✅ **Prediksi WMA** — forecast permintaan bulan berikutnya
- ✅ **Akurasi MAE & MAPE** — evaluasi ketepatan prediksi

### 📈 Monitoring & Laporan
- ✅ Dashboard real-time dengan grafik pergerakan stok
- ✅ Monitoring barang stok menipis & habis
- ✅ Laporan stok, barang masuk/keluar, pengadaan, prediksi
- ✅ Export PDF & Excel semua laporan
- ✅ Activity log seluruh aktivitas pengguna

### 🔔 Notifikasi
- ✅ In-app notification untuk stok menipis
- ✅ Notifikasi reorder point tercapai
- ✅ Notifikasi permintaan & pengadaan baru

---

## 🛠️ Teknologi

### Backend
| Teknologi | Versi | Keterangan |
|-----------|-------|------------|
| PHP | 8.2+ | Bahasa pemrograman |
| Laravel | 11.x | Framework utama |
| MySQL | 8.0+ | Database |

### Frontend
| Teknologi | Keterangan |
|-----------|------------|
| Blade | Template engine Laravel |
| Tailwind CSS 3.x | CSS framework utility-first |
| Alpine.js | JavaScript framework ringan |
| Chart.js 4.x | Visualisasi grafik |

### Package Laravel
| Package | Keterangan |
|---------|------------|
| `laravel/breeze` | Authentication starter |
| `spatie/laravel-permission` | Role & Permission management |
| `spatie/laravel-activitylog` | Audit trail aktivitas user |
| `barryvdh/laravel-dompdf` | Export laporan PDF |
| `maatwebsite/excel` | Export laporan Excel |
| `intervention/image` | Kompresi & resize foto |
| `laravel/telescope` | Debugging (development) |

---

## 💻 Persyaratan Sistem

Sebelum instalasi, pastikan sistem Anda memenuhi persyaratan berikut:

| Komponen | Minimum | Rekomendasi |
|----------|---------|-------------|
| PHP | 8.2 | 8.2+ |
| Composer | 2.x | 2.7+ |
| Node.js | 18.x | 20.x LTS |
| NPM | 9.x | 10.x |
| MySQL | 8.0 | 8.0+ |
| RAM | 512 MB | 1 GB+ |

### Software yang Dibutuhkan
- [XAMPP](https://www.apachefriends.org/) (Windows) — sudah include PHP, MySQL, Apache
- [Composer](https://getcomposer.org/) — PHP package manager
- [Node.js](https://nodejs.org/) — untuk build assets Tailwind CSS
- [Git](https://git-scm.com/) — version control

---

## 🚀 Instalasi

Ikuti langkah-langkah berikut secara berurutan.

### Langkah 1 — Clone Repository

```bash
git clone https://github.com/YOUR_USERNAME/inventory-system.git
cd inventory-system
```

### Langkah 2 — Install PHP Dependencies

```bash
composer install
```

> ⏳ Proses ini membutuhkan waktu beberapa menit tergantung koneksi internet.

### Langkah 3 — Konfigurasi Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Langkah 4 — Konfigurasi Database

Buka file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_system
DB_USERNAME=root
DB_PASSWORD=
```

> 💡 Untuk XAMPP default: `DB_USERNAME=root` dan `DB_PASSWORD=` (kosong)

### Langkah 5 — Buat Database

Buka **phpMyAdmin** di `http://localhost/phpmyadmin`, lalu:
1. Klik **"New"**
2. Nama database: `inventory_system`
3. Collation: `utf8mb4_unicode_ci`
4. Klik **"Create"**

Atau via command line MySQL:

```sql
CREATE DATABASE inventory_system 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

### Langkah 6 — Jalankan Migrasi & Seeder

```bash
# Jalankan semua migrasi database
php artisan migrate

# Isi data awal (roles, users, sample data)
php artisan db:seed
```

> ✅ Seeder akan membuat akun demo dan 10 barang contoh secara otomatis.

### Langkah 7 — Install Node Dependencies & Build Assets

```bash
# Install Node.js dependencies
npm install

# Build assets untuk production
npm run build
```

### Langkah 8 — Setup Storage

```bash
# Buat symbolic link untuk storage publik
php artisan storage:link
```

### Langkah 9 — Jalankan Aplikasi

```bash
php artisan serve
```

Buka browser dan akses: **`http://localhost:8000`**

---

## ⚙️ Konfigurasi

### Konfigurasi Queue (Untuk Notifikasi)

Notifikasi menggunakan Laravel Queue. Jalankan queue worker di terminal terpisah:

```bash
php artisan queue:work --tries=3
```

> 💡 Untuk development, Anda bisa mengubah `QUEUE_CONNECTION=sync` di `.env`
> agar notifikasi langsung diproses tanpa perlu queue worker.

### Konfigurasi Telescope (Debug Tool)

Laravel Telescope tersedia di: `http://localhost:8000/telescope`

Untuk menonaktifkan di production:
```env
TELESCOPE_ENABLED=false
```

### Reset Database (Jika Diperlukan)

```bash
# Hapus semua tabel dan buat ulang + isi data awal
php artisan migrate:fresh --seed
```

---

## 👥 Akun Demo

Setelah menjalankan seeder, akun-akun berikut tersedia:

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Admin Gudang** | admin@inventory.com | password | Full access — kelola stok, barang, EOQ |
| **Manajer** | manajer@inventory.com | password | Pengadaan, analisis, laporan |
| **User** | user@inventory.com | password | Lihat stok, buat permintaan |

---

## 📁 Struktur Folder

```
inventory-system/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CheckLowStock.php       # Command cek stok otomatis
│   ├── Exceptions/
│   │   ├── InsufficientDataException.php
│   │   └── InsufficientStockException.php
│   ├── Exports/                        # Export Excel
│   │   ├── StockReportExport.php
│   │   ├── StockInReportExport.php
│   │   ├── StockOutReportExport.php
│   │   └── ProcurementReportExport.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                  # Controller Admin
│   │   │   ├── Manajer/                # Controller Manajer
│   │   │   ├── User/                   # Controller User
│   │   │   ├── DashboardController.php
│   │   │   ├── NotificationController.php
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   └── EnsureUserIsActive.php
│   │   └── Requests/                   # Form Validation
│   │       ├── Admin/
│   │       ├── Manajer/
│   │       └── User/
│   ├── Models/                         # Eloquent Models
│   │   ├── User.php
│   │   ├── Item.php
│   │   ├── Category.php
│   │   ├── Supplier.php
│   │   ├── StockIn.php
│   │   ├── StockOut.php
│   │   ├── ItemRequest.php
│   │   ├── Procurement.php
│   │   ├── DemandHistory.php
│   │   ├── EoqCalculation.php
│   │   └── Forecast.php
│   ├── Notifications/                  # Notifikasi
│   │   ├── LowStockNotification.php
│   │   ├── ReorderPointNotification.php
│   │   ├── ItemRequestNotification.php
│   │   └── ProcurementNotification.php
│   ├── Observers/                      # Model Observers
│   │   ├── ItemObserver.php
│   │   ├── CategoryObserver.php
│   │   └── SupplierObserver.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/                       # Business Logic
│       ├── CacheService.php
│       ├── DocumentNumberService.php
│       ├── EoqService.php              # Kalkulasi EOQ, SS, ROP
│       ├── ForecastService.php         # Weighted Moving Average
│       ├── NotificationService.php
│       └── StockService.php            # Manajemen pergerakan stok
├── database/
│   ├── factories/                      # Data dummy factories
│   ├── migrations/                     # Database schema
│   └── seeders/                        # Data awal
│       ├── DatabaseSeeder.php
│       ├── RolePermissionSeeder.php
│       ├── UserSeeder.php
│       ├── MasterDataSeeder.php
│       └── DemandHistorySeeder.php
├── resources/
│   └── views/
│       ├── admin/                      # Views Admin
│       ├── manajer/                    # Views Manajer
│       ├── user/                       # Views User
│       ├── layouts/                    # Layout utama
│       ├── components/                 # Komponen reusable
│       ├── pdf/                        # Template PDF
│       └── auth/                       # Halaman login
├── routes/
│   ├── web.php                         # Web routes
│   └── console.php                     # Scheduled commands
├── tests/
│   └── Unit/
│       ├── EoqServiceTest.php          # Unit test EOQ
│       └── ForecastServiceTest.php     # Unit test WMA
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

## 🔬 Metodologi

### Economic Order Quantity (EOQ)

Formula untuk menentukan jumlah pemesanan yang meminimalkan total biaya
persediaan:

$$EOQ = \sqrt{\frac{2DS}{H}}$$

| Notasi | Keterangan |
|--------|------------|
| **D** | Demand tahunan (unit/tahun) |
| **S** | Biaya pemesanan per pesan (Rp) |
| **H** | Biaya penyimpanan per unit per tahun (Rp) |

### Safety Stock

Stok pengaman untuk mengantisipasi variasi permintaan:

$$SS = (d_{max} - d_{avg}) \times L$$

| Notasi | Keterangan |
|--------|------------|
| **d_max** | Permintaan harian maksimum |
| **d_avg** | Permintaan harian rata-rata |
| **L** | Lead time (hari) |

### Reorder Point (ROP)

Titik pemesanan ulang — kapan harus memesan:

$$ROP = (d_{avg} \times L) + SS$$

### Weighted Moving Average (WMA)

Prediksi permintaan dengan bobot lebih besar pada data terbaru:

$$WMA = \frac{\sum_{i=1}^{n} (W_i \times D_i)}{\sum_{i=1}^{n} W_i}$$

**Contoh WMA 3 Periode:**

| Periode | Data | Bobot | Bobot × Data |
|---------|------|-------|-------------|
| Januari | 100 | 1 | 100 |
| Februari | 120 | 2 | 240 |
| Maret | 110 | 3 | 330 |
| **Total** | | **6** | **670** |

$$WMA_{April} = \frac{670}{6} = 111.67$$

### Evaluasi Akurasi (MAPE)

$$MAPE = \frac{1}{n} \sum_{i=1}^{n} \left|\frac{A_i - F_i}{A_i}\right| \times 100\%$$

| Nilai MAPE | Interpretasi |
|------------|-------------|
| < 10% | Sangat Akurat |
| 10% - 20% | Akurat |
| 20% - 50% | Cukup Akurat |
| > 50% | Kurang Akurat |

---

## 📸 Screenshot

### Halaman Login
> Halaman login dengan tampilan modern dan daftar akun demo.

### Dashboard Admin
> Dashboard dengan KPI cards, grafik pergerakan stok, dan notifikasi stok menipis.

### Perhitungan EOQ
> Tabel perhitungan EOQ lengkap dengan interpretasi otomatis.

### Prediksi WMA
> Grafik prediksi aktual vs WMA dengan evaluasi akurasi MAPE.

---

## 🧪 Menjalankan Unit Test

```bash
# Jalankan semua test
php artisan test

# Test spesifik EOQ
php artisan test --filter=EoqServiceTest

# Test spesifik WMA
php artisan test --filter=ForecastServiceTest
```

Expected output:

```
PASS  Tests\Unit\EoqServiceTest
✓ it calculates eoq correctly            0.01s
✓ it calculates safety stock correctly   0.01s
✓ it calculates rop correctly            0.01s
✓ it returns zero for invalid holding cost

PASS  Tests\Unit\ForecastServiceTest
✓ it calculates wma correctly            0.01s
✓ it generates correct bobot for n3
✓ it gives more weight to recent data
✓ it calculates next periode correctly
```

---

## 🔧 Perintah Artisan Berguna

```bash
# Cek stok menipis & kirim notifikasi
php artisan inventory:check-stock

# Jalankan queue worker
php artisan queue:work --tries=3

# Bersihkan cache
php artisan optimize:clear

# Rebuild cache production
php artisan optimize

# Lihat semua route
php artisan route:list --compact

# Reset database + isi ulang data
php artisan migrate:fresh --seed
```

---

## ❓ Troubleshooting

### Error: `php_fileinfo` extension not loaded
```bash
# Aktifkan di php.ini
extension=fileinfo
```

### Error: `Class 'ZipArchive' not found`
```bash
# Aktifkan di php.ini
extension=zip
```

### Error: Gambar tidak tampil
```bash
php artisan storage:link
```

### Error: Queue tidak berjalan
```bash
# Ubah ke sync untuk development
QUEUE_CONNECTION=sync
```

### Error: `composer install` gagal
```bash
composer install --ignore-platform-reqs
```

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan **Tugas Akhir / Skripsi** pada:

- **Program Studi:** Teknik Informatika
- **Universitas:** Universitas Muhammadiyah Jakarta
- **Tahun:** 2024

---

## 👨‍💻 Developer

**Nama Mahasiswa**
- GitHub: [@your-github](https://github.com/your-github)
- Email: your-email@example.com
- NIM: XXXXXXXXXX

---

<div align="center">

Dibuat dengan ❤️ menggunakan Laravel 11 & Tailwind CSS

⭐ Star repo ini jika membantu!

</div>