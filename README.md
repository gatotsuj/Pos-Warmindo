# 🍜 Warmindo POS System

Sistem Point of Sale (POS) modern berbasis Web khusus untuk mengelola bisnis Warmindo (Warung Makan Indomie). Dibangun menggunakan **Laravel 10/11**, **Alpine.js**, dan **Tailwind CSS**. 

Sistem ini dirancang untuk mempercepat proses pemesanan, manajemen stok produk secara real-time, cetak struk, fitur *void* (pembatalan), dan dilengkapi dengan *dashboard* analitik harian.

---

## 📋 Prasyarat Sistem

Sebelum menginstal aplikasi ini, pastikan sistem/server Anda sudah terinstal:

- **PHP** >= 8.1
- **Composer** (Dependency Manager untuk PHP)
- **Node.js** & **NPM** (Untuk mengompilasi aset CSS/JS)
- **MySQL** atau **MariaDB** (Database)
- Web Server lokal seperti **Laragon** (Direkomendasikan untuk Windows), **XAMPP**, atau **Valet**

---

## 🚀 Cara Instalasi

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan proyek ini di *local environment* Anda dari *repository* GitHub:

### 1. Clone Repository
Buka terminal (Command Prompt / PowerShell / Git Bash) di folder server lokal Anda (misal `C:\laragon\www` atau `htdocs`), lalu jalankan perintah berikut:
```bash
git clone https://github.com/gatotsuj/Pos-Warmindo.git
cd Pos-Warmindo
```
*(Catatan: Pastikan Anda mengganti URL GitHub dengan URL repositori yang valid).*

### 2. Install Dependensi PHP (Composer)
Unduh seluruh pustaka *backend* (vendor) yang dibutuhkan oleh Laravel:
```bash
composer install
```

### 3. Install Dependensi Frontend (NPM)
Unduh seluruh pustaka *frontend* (Tailwind CSS, Alpine.js, dll):
```bash
npm install
```

### 4. Konfigurasi Environment (`.env`)
Aplikasi Laravel membutuhkan file `.env`. Salin template yang sudah disediakan:
- **Windows:** `copy .env.example .env`
- **Mac/Linux:** `cp .env.example .env`

Buka file `.env` yang baru dibuat di teks editor Anda, lalu sesuaikan koneksi database. Pastikan Anda sudah membuat *database kosong* (misal bernama `warmindo_pos`) di MySQL (via phpMyAdmin / HeidiSQL):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warmindo_pos
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate Application Key
Buat kunci keamanan unik untuk aplikasi Anda:
```bash
php artisan key:generate
```

### 6. Link Folder Storage
Aplikasi ini menyimpan gambar produk secara lokal. Agar gambar bisa diakses dari *browser*, Anda wajib membuat *symlink*:
```bash
php artisan storage:link
```

### 7. Migrasi Database dan Seeding
Jalankan migrasi untuk membuat tabel-tabel database sekaligus memasukkan data dummy (Produk, Kategori, dan Akun Admin):
```bash
php artisan migrate:fresh --seed
```

### 8. Kompilasi Aset Frontend
Tampilan aplikasi (desain *glassmorphism*, UI modern) membutuhkan file CSS yang harus dikompilasi oleh Vite. Jalankan:
```bash
npm run build
```

### 9. Jalankan Aplikasi
Jika Anda menggunakan **Laragon**, Anda dapat langsung membuka browser dan mengakses: `http://pos-system-warmindo.test`

Atau, Anda dapat menggunakan server internal artisan:
```bash
php artisan serve
```
Aplikasi akan berjalan di alamat `http://localhost:8000`.

---

## 🔐 Kredensial Akses Default

Setelah proses seeder berhasil dijalankan, Anda bisa masuk (Login) menggunakan data default administrator:

*   **Email:** `admin@admin.com` 
*   **Password:** `password` 

*(Anda bisa mengubah kredensial ini nanti di pengaturan profil atau dengan mengedit seeder `database/seeders/AdminUserSeeder.php`)*.

---

## 🛠 Fitur Utama

- **Dashboard Analitik**: Pantau tren pendapatan, item terlaris, dan rekap transaksi 7 hari terakhir.
- **Transaksi POS Modern**: Layar kasir (Point of Sale) interaktif, dengan perhitungan stok, pajak, dan diskon yang akurat tanpa loading pindah halaman.
- **Manajemen Produk & Kategori**: UI yang *user-friendly* untuk menambah/mengedit produk dan foto.
- **Riwayat Stok & Void**: Melacak *Stock Movement* dan mendukung fitur batal (Void) transaksi dengan pengembalian stok yang aman (Transaction DB).
- **Responsif & Ringan**: Estetika modern premium menggunakan TailwindCSS dengan warna khas *Indomie* (Merah, Kuning, Hijau) dan transisi yang *smooth*.
