# Warmindo POS & SaaS Multi-Tenant Platform

![Laravel](https://img.shields.io/badge/Laravel-10%2F11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-RAM_Cache-DC382D?style=for-the-badge&logo=redis&logoColor=white)
![Testing](https://img.shields.io/badge/PHPUnit-100%25_Pass-4642C4?style=for-the-badge&logo=php&logoColor=white)
![Standard Akuntansi](https://img.shields.io/badge/Akuntansi-SAK%20EMKM-0052CC?style=for-the-badge)

Sistem Point of Sale (POS) & Platform SaaS Multi-Tenant Modern berbasis Web khusus untuk mengelola bisnis kuliner, Warmindo (Warung Makan Indomie), dan UMKM Indonesia. Dibangun menggunakan **Laravel**, **Alpine.js**, **Tailwind CSS**, **Redis RAM Cache**, serta dilengkapi dengan **Mesin Akuntansi Otomatis Standar SAK EMKM** dan **Skrip Testing Otomatis (PHPUnit/Pest)**.

---

## Fitur Utama & Modul Sistem

### 1. PWA POS Kasir & Manajemen Shift Kasir
- **Progressive Web App (PWA)**: Dapat diinstal di HP/Tablet Android & iOS dengan kemampuan simpan offline.
- **Kasir Drawer Interface**: Tampilan POS interaktif dengan keranjang belanja slide-over, pencarian produk cepat, serta filter kategori.
- **Manajemen Shift Kasir & Rekap Laci Kas (`cashier_shifts`)**:
  - Modal input saldo kas awal saat kasir membuka shift (dengan tombol Batal & Close UX).
  - Ringkasan transaksi kas vs non-kas selama shift berlangsung.
  - Modal penutupan shift & rekonsiliasi laci kasir (penghitungan uang fisik vs saldo sistem + selisih kas).
- **Pengaturan Metode Pembayaran Toko**: Admin toko dapat mengaktifkan/menonaktifkan metode pembayaran **Tunai (Cash)**, **QRIS / E-Wallet**, dan **Kartu Debit/Kredit**.
- **Cetak Struk Termal**: Terintegrasi logo toko, nama kasir bertugas, rincian diskon, pajak, serta footer struk dinamis.

### 2. Arsitektur SaaS Multi-Tenant & Branding Toko
- **Isolasi Data Tenant (`BelongsToTenant`)**: Setiap data transaksi, stok, kasir, dan akun keuangan terpisah dengan aman antar-tenant/warung.
- **Superadmin Switch Context**: Fitur khusus Superadmin untuk berpindah (*switch*) ke konteks toko mana pun untuk audit atau penanganan kendala.
- **Kustomisasi Warna Tema Brand Toko**: Pilihan 6 tema warna (Indomie Red, Emerald Green, Royal Blue, Deep Indigo, Amber Gold, Dark Slate) yang otomatis mengubah gradien sidebar dan button.
- **Dynamic Store Logo**: Upload logo toko yang langsung tampil di sidebar aplikasi dan cetak struk termal.

### 3. Mesin Akuntansi Standar SAK EMKM (Khusus Admin/Superadmin)
- **Bagan Akun Standar 4-Digit (Chart of Accounts / COA)**: Templat akun standar Indonesia (Aset, Kewajiban, Ekuitas, Pendapatan, HPP, Beban) dilengkapi fitur *Sync & Reset Templat SaaS*.
- **Auto-Journaling Engine**:
  - Transaksi checkout POS otomatis membentuk Jurnal Umum Kas/Bank vs Pendapatan & HPP.
  - Pembatalan transaksi (*Void*) otomatis membuat Jurnal pembalik.
- **Manajemen Pengeluaran Kas (Petty Cash)**: Pencatatan beban operasional kasir dengan validasi nominal presisi (`step="any"`).
- **Laporan Keuangan Otomatis**:
  - **Laporan Laba Rugi** (*Income Statement*)
  - **Laporan Neraca Seimbang** (*Balance Sheet*)
  - **Buku Besar** (*General Ledger*) per akun
  - **Jurnal Umum** (*General Journal*)

### 4. Performa Infrastruktur, Redis Caching, & Observability
- **Redis Cache & Session Store**: Menggunakan `predis` untuk menyimpan sesi kasir dan memproses antrean job di RAM.
- **Master Data Redis Caching**:
  - Caching otomatis master produk aktif (`tenant_{$id}_active_products`) dan pengaturan toko (`tenant_{$id}_receipt_settings`).
  - Auto-invalidasi cache (`flushCache`) secara otomatis saat ada penambahan/pengeditan produk atau stok.
- **Laravel Pulse Server Monitoring (`/pulse`)**: Pemantauan real-time performa server, penggunaan CPU, memori RAM, query SQL lambat (*slow queries*), dan log exception error.
- **Laravel Horizon Dashboard (`/horizon`)**: Management dashboard antrean Redis worker real-time.

### 5. Manajemen Inventori & Satuan Universal UMKM
- **Multi-Satuan Produk**: Mendukung satuan universal UMKM (**Pcs, Kg, Porsi, Paket, Unit, Meter, Jam**).
- **Harga Modal / HPP (`cost_price`)**: Input harga beli/modal produk untuk perhitungan Laba Kotor (*Gross Margin*).
- **Log Pergerakan Stok & Warning Alert**: Peringatan otomatis ketika stok produk mencapai batas kritis (low stock).

### 6. Analitik & Business Intelligence (BI)
- **Analisis Jam Sibuk Toko (*Peak Hours Chart*)**: Grafik frekuensi transaksi per jam (06.00 – 23.00 WIB) untuk menentukan jadwal shift kasir.
- **Ratio Metode Pembayaran**: Grafik persentase transaksi Cash vs QRIS vs Card.
- **Produk Terlaris & Produk Paling Menguntungkan (*Top Profit Products*)**: Membandingkan produk terlaris secara unit vs produk penghasil Laba Kotor Nominal Rp terbesar.

### 7. Manajemen Peran & Reset Password Instan
- **Reset Password Instan oleh Admin**: Admin Toko dapat melakukan reset password kasir/staff langsung dari tabel manajemen pengguna (`/admin/users`) via modal popup tanpa memerlukan verifikasi email.
- **Identitas Kasir pada Struk**: Menampilkan nama kasir yang bertugas menangani transaksi pada struk fisik.
- **100% Clean SVG Vector Icons**: Bebas emoji keyboard untuk tampilan antarmuka yang konsisten dan profesional.

---

## Skrip Pengujian Otomatis (Automated Test Suite)

Aplikasi dilengkapi dengan skrip testing otomatis berbasis **PHPUnit / Pest** untuk memastikan keandalan transaksi kasir, stok, shift, dan laporan keuangan:

```bash
php artisan test
```

Hasil Eksekusi Test:
```text
  Tests:    30 passed (75 assertions)
  Duration: 3.73s
```
Seluruh 30 tes otomatis **100% PASS**.

---

## Prasyarat Sistem

Sebelum menginstal aplikasi ini, pastikan sistem/server Anda sudah terinstal:
- **PHP** >= 8.2
- **Composer** (Dependency Manager untuk PHP)
- **Node.js** & **NPM** (Untuk mengompilasi aset CSS/JS)
- **MySQL** atau **MariaDB** (Database Engine)
- **Redis Server** (Koneksi Cache, Session, & Queue)
- Web Server lokal seperti **Laragon** (Windows), **XAMPP**, atau **Nginx/Apache**

---

## Cara Instalasi & Run Local

### 1. Clone Repository
```bash
git clone https://github.com/gatotsuj/Pos-Warmindo.git
cd Pos-Warmindo
```

### 2. Install Dependensi Backend (Composer)
```bash
composer install
```

### 3. Install & Compile Frontend Assets (NPM)
```bash
npm install
npm run build
```

### 4. Konfigurasi Environment (`.env`)
Salin file template `.env.example`:
```bash
copy .env.example .env
```
Sesuaikan konfigurasi database MySQL & Redis pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_warmindo
DB_USERNAME=root
DB_PASSWORD=

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### 5. Generate Application Key & Migrasi Database
```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### 6. Jalankan Worker Redis & Server Lokal
Terminal 1 (Server Utama):
```bash
php artisan serve
```
Terminal 2 (Queue Worker Redis):
```bash
php artisan queue:work redis --tries=3 --timeout=90
```

Akses aplikasi melalui browser di `http://127.0.0.1:8000`.
- Dashboard Server Monitoring Pulse: `http://127.0.0.1:8000/pulse`
- Dashboard Queue Worker Horizon: `http://127.0.0.1:8000/horizon`

---

## Hak Akses Default (Seeder)

| Peran (Role) | Email Login | Password | Akses |
| :--- | :--- | :--- | :--- |
| **Superadmin** | `superadmin@pos.com` | `password` | Seluruh Tenant, SaaS Admin & Switcher |
| **Admin Toko** | `admin@warmindo.com` | `password` | Produk, Audit Shift, Struk, **Akuntansi SAK** |
| **Kasir** | `kasir@warmindo.com` | `password` | POS Kasir (`/pos`), Buka/Tutup Shift Kasir |

---

## Struktur Direktori Utama

```text
Pos-System-warmindo/
├── app/
│   ├── Http/Controllers/
│   │   ├── Accounting/            # Controller Jurnal, Akun, Laba Rugi, Neraca, Pengeluaran
│   │   ├── Admin/                 # Controller Produk, User, Settings, Reports
│   │   ├── Cashier/               # Controller POS Kasir Engine
│   │   └── CashierShiftController # Controller Shift & Rekap Kas Laci
│   ├── Jobs/                      # Queue Jobs (TestWarmindoJob)
│   ├── Models/
│   │   ├── Akuntansi/             # Model Akun, JurnalHeader, JurnalDetail, Pengeluaran
│   │   ├── CashierShift.php       # Model Shift Kasir
│   │   ├── Product.php            # Model Produk (Satuan, Cost Price, Stock)
│   │   └── ReceiptSetting.php     # Model Pengaturan Struk, Logo, Tema Warna, Payment Toggle
│   ├── Repositories/              # Pattern Repository Interface & Eloquent
│   └── Services/                  # AkuntansiService (Engine Auto-Journaling SAK)
├── database/
│   └── migrations/                # Skema Database & Migrasi System (Termasuk Pulse)
├── resources/
│   ├── views/
│   │   ├── accounting/            # Template Blade Akuntansi SAK
│   │   ├── admin/                 # Template Blade Dashboard Admin, Shift Audit, Settings
│   │   ├── cashier/               # Template Blade POS Kasir (Drawer, Modals)
│   │   ├── profile/               # Template Blade Profile User Redesign
│   │   └── layouts/               # Master Layout App & Dynamic Theme Color
├── tests/
│   └── Feature/                   # Skrip Testing Otomatis (PosCheckoutTest, CashierShiftTest)
└── routes/
    └── web.php                    # Middleware Auth, Tenant & Role Scoping Routes
```

---

## Lisensi
Dipersembahkan untuk pengembangan sistem POS & SaaS UMKM Kuliner Indonesia. Hak Cipta © 2026.
