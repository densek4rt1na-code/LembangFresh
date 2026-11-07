# LembangFresh 🥕
**Sistem Informasi Berbasis GIS untuk Optimalisasi Pemasaran dan Distribusi Hasil Hortikultura di Kecamatan Lembang, Kabupaten Bandung Barat**  
*Tugas Akhir Pemrograman Web Semester 2*

---

## 🌟 Ringkasan Aplikasi & Pemenuhan Tugas

Proyek ini telah dikonversi dan dibangun secara penuh menggunakan framework **Laravel 11/13** dengan arsitektur **HMVC (Hierarchical Model-View-Controller)**, sistem Blade Template modular, sistem otentikasi multi-role aman berbasis hash, proteksi middleware, CRUD synchronous & AJAX dengan upload berkas, serta integrasi peta spasial **GIS (Geographic Information System)** menggunakan **Leaflet.js** untuk wilayah Kecamatan Lembang.

---

## 📋 Matriks Pemenuhan Ketentuan Tugas

### 1. Ketentuan Blade Template
* **Layout Utama:** Satu master layout terpusat pada [`resources/views/layouts/app.blade.php`](file:///Users/denseaikartina/Documents/Kelompok/Tubes/resources/views/layouts/app.blade.php) yang memuat `<head>`, Bootstrap 5.3, FontAwesome 6, Leaflet CSS/JS, Navbar, Flash Alerts, Footer, dan `@stack('scripts')`. Diwariskan ke seluruh halaman menggunakan `@extends('layouts.app')`, `@section('content')`, dan `@yield('content')`.
* **Directive Blade:**
  * **Percabangan:** Menggunakan `@if`, `@elseif`, `@else`, `@empty`, `@auth`, `@guest`, `@unless` (contoh pada layout, welcome view, product-detail, dan dashboard).
  * **Perulangan:** Menggunakan `@foreach` dan `@forelse` (contoh pada daftar produk, tabel hasil panen, marker GIS, dan filter pencarian).
* **Komponen & Partial:**
  * **Komponen Blade:** `<x-status-badge>`, `<x-alert>`, `<x-product-card>`.
  * **Partial Blade:** `@include('partials.navbar')`, `@include('partials.footer')`.
* **Named Routes:** Seluruh rute aplikasi memiliki penamaan standar (`route('home')`, `route('login')`, `route('harvests.index')`, `route('logistics.index')`, `route('gis.index')`, `route('matching.index')`, dll).

---

### 2. Ketentuan Authentication & Authorization Berbasis Role
* **Metode Pembuatan:** Menggunakan implementasi autentikasi kustom terstruktur (`AuthController` dengan `Auth::attempt`, Session Regeneration, CSRF token, dan validasi form kustom berbahasa Indonesia).  
  * *Alasan Pemilihan pada Laporan:* Pembuatan manual memberikan kontrol penuh atas struktur data multi-aktor (Petani, Pembeli, Mitra, Admin), memastikan tidak ada konflik dependency/package pihak ketiga saat proses penilaian offline, serta memungkinkan validasi terintegrasi untuk atribut spasial dan koordinat GIS sejak tahap registrasi.
* **Fitur Autentikasi:** Sedia fitur Login, Register (dengan seleksi role: Petani, Pembeli, Mitra), dan Logout yang terproteksi CSRF.
* **Proteksi Middleware:** 
  * Middleware `auth` untuk memproteksi seluruh dashboard dan modul pengelolaan data.
  * Middleware `role:admin,petani`, `role:admin,mitra` untuk membatasi hak akses antar aktor pengguna.
* **Akun Dummy & Hashing Password:** 
  * Minimal 5 akun dummy dibuat menggunakan **Faker** (`Faker\Factory::create('id_ID')`) pada `database/seeders/UserSeeder.php` dan terdaftar di `database/seeders/DatabaseSeeder.php`.
  * Seluruh kata sandi tersimpan dalam bentuk **Hash Bcrypt** (`Hash::make(...)`).

#### 🔑 Akun Demo Pengujian Sistem:
| Role | Email | Password | Keterangan & Lokasi di Lembang |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@lembangfresh.test` | `admin123` | Administrator Sistem (Akses seluruh modul & GIS) |
| **Petani 1** | `agus@lembangfresh.test` | `petani123` | Pak Agus (Desa Cikidang - Layanan antar aktif radius 5 km) |
| **Petani 2** | `siti@lembangfresh.test` | `petani123` | Bu Siti (Desa Cibodas - Layanan antar nonaktif, via Mitra) |
| **Pembeli** | `salad@lembangfresh.test` | `pembeli123` | UMKM Salad Fresh (Jl. Panorama Lembang) |
| **Mitra** | `budi@lembangfresh.test` | `mitra123` | Pak Budi Logistik (Armada Pickup L300 D 1234 XY) |
| *Dummy 1-5* | `dummy1_...@lembangfresh.test` | `password` | 5 Akun Dummy yang digenerate oleh Faker |

---

### 3. Ketentuan CRUD Synchronous dengan Upload Berkas (Modul 1: Harvest)
* **Arsitektur:** Modul HMVC pada folder `app/Modules/Harvest/` menggunakan **Resource Controller** (`HarvestController`) dan **Resource Route** (`Route::resource('harvests', HarvestController::class)`).
* **Validasi Upload Berkas:** Validasi file gambar foto komoditas (`image|mimes:jpeg,png,jpg,webp|max:2048`).
* **Penyimpanan & Penghapusan Otomatis Storage:**
  * File tersimpan pada `storage/app/public/harvests/` dan dapat dilihat langsung melalui browser.
  * Saat data diubah dengan foto baru, file lama otomatis dihapus dari disk menggunakan `Storage::disk('public')->delete(...)`.
  * Saat data dihapus (destroy), file foto pada storage otomatis terhapus bersama record database.
* **Fitur Rekomendasi Jalur Pemasaran (SDD Bab 4):**
  * **Grade A:** Otomatis direkomendasikan ke *Pasar Normal* (Supermarket, Hotel, Resto).
  * **Grade B:** Otomatis direkomendasikan ke *Pasar Alternatif* (UMKM Kuliner, Katering).
  * **Grade C:** Otomatis diarahkan ke *Jalur Pengolahan* (Industri Saus, Sambal, Keripik).

---

### 4. Ketentuan Arsitektur HMVC & CRUD AJAX dengan Upload Berkas (Modul 2: Logistics)
* **Arsitektur:** Modul HMVC pada folder `app/Modules/Logistics/` dengan controller `LogisticsAjaxController`.
* **Fitur AJAX Penuh (Tanpa Reload):**
  * **Create:** Modal tambah pengiriman dengan unggah berkas surat jalan via `FormData` dan AJAX POST (`/logistics/api/store`).
  * **Read:** Memuat dan memfilter data penugasan secara real-time via AJAX GET (`/logistics/api/data`).
  * **Update:** Modal ubah data dan ganti berkas via AJAX POST (`/logistics/api/{id}/update`).
  * **Delete:** Hapus data pengiriman dan hapus file berkas dari storage via AJAX DELETE (`/logistics/api/{id}`).
* **Fitur Wajib Buka Kembali Berkas Upload:** 
  * Tombol **"Buka Berkas"** membuka modal interaktif yang menampilkan pratinjau dokumen/gambar surat jalan.
  * Tombol **"Buka di Tab Baru"** dan **"Unduh Berkas"** (`/logistics/{id}/download`) memungkinkan evaluasi berkas yang tersimpan di storage.

---

### 5. Fitur GIS (Geographic Information System) & Supply-Demand Matching
* **Peta Spasial Lembang:** Menampilkan titik persebaran petani, pembeli, dan mitra pengangkut di Kecamatan Lembang.
* **Kalkulasi Jarak Haversine:** Menghitung jarak tempuh akurat dalam satuan kilometer antara koordinat kebun dan lokasi pembeli.
* **Kalkulasi Biaya Pengiriman Sesuai SDD Bab 7:**
  1. **Ambil Sendiri:** Rp 0 (Gratis).
  2. **Diantar Petani:** Tarif tetap petani (misal Rp 25.000) jika jarak berada di dalam radius antar petani.
  3. **Mitra Pengangkut:** Formula resmi: `Tarif Dasar Rp 50.000 + (Jarak km x Rp 5.000)`.
* **Supply-Demand Matching Engine (SDD Bab 3.3 & 13):** Mempertemukan pasokan komoditas petani dengan kebutuhan terbuka pembeli berdasarkan kesesuaian komoditas, grade kualitas, dan batas harga maksimal.

---

## 🚀 Panduan Menjalankan Aplikasi

### Opsi A: Menggunakan SQLite (Langsung Siap Pakai)
Database SQLite telah diinisialisasi dan di-seed dengan data lengkap:
```bash
php artisan serve
```
Buka browser pada: `http://localhost:8000`

---

### Opsi B: Menggunakan MySQL & phpMyAdmin (XAMPP)
1. Buka aplikasi **XAMPP** dan pastikan service **MySQL** sudah berjalan (Running).
2. Database bernama **`db_lembangfresh`** sudah berhasil dibuat di MySQL lokal Anda.
3. Jika ingin mengimpor ulang melalui **phpMyAdmin** (`http://localhost/phpmyadmin`):
   * Pilih database **`db_lembangfresh`**
   * Klik tab **Import**
   * Pilih file SQL dump: **`db_lembangfresh.sql`** (tersedia di root project atau folder `database/db_lembangfresh.sql`)
4. Konfigurasi pada file [`.env`](file:///Users/denseaikartina/Documents/Kelompok/Tubes/.env) sudah diarahkan ke:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_lembangfresh
   DB_USERNAME=root
   DB_PASSWORD=
   ```
6. Jalankan server Laravel:
   ```bash
   php artisan serve
   ```
7. *Bukti Pemeriksaan phpMyAdmin:*
   * Tabel `users`: Periksa kolom `password`, seluruh password tersimpan dalam bentuk hash (`$2y$12$...`).
   * Tabel `harvests`: Periksa kolom `photo_path` yang memuat path penyimpanan berkas gambar (contoh: `harvests/wortel.png`).
   * Tabel `logistics_shipments`: Periksa kolom `document_path` yang memuat path berkas surat jalan (contoh: `logistics/surat_jalan_001.png`).
   * Folder `storage/app/public/`: Seluruh berkas tersimpan pada folder `harvests/` dan `logistics/` serta terhubung dengan symlink `public/storage`.

---

## 🧪 Pengujian Otomatis (Test Suite)
Untuk memverifikasi keandalan seluruh rute, otentikasi, middleware hak akses role, CRUD, serta upload dan penghapusan storage:
```bash
php artisan test
```
*Hasil Pengujian:*
```text
   PASS  Tests\Feature\LembangFreshTest
  ✓ public pages can be rendered
  ✓ guest is redirected from protected pages
  ✓ dashboard renders for all roles
  ✓ user authentication flow
  ✓ role based authorization
  ✓ synchronous crud with file upload and deletion
  ✓ ajax crud with document upload and reopen
  ✓ order creation with mitra integrates with logistics
   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

  Tests:    10 passed (82 assertions)
  Duration: 0.54s
```
