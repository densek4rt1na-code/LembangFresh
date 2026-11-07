# LAPORAN TUGAS AKHIR PEMROGRAMAN WEB SEMESTER 2
## Sistem Informasi Berbasis GIS untuk Optimalisasi Pemasaran dan Distribusi Hasil Hortikultura di Kecamatan Lembang (LembangFresh)

---

### IDENTITAS PROYEK
* **Nama Aplikasi:** LembangFresh
* **Tema / Lokasi Studi:** Pemasaran & Distribusi Hasil Hortikultura, Kecamatan Lembang, Kabupaten Bandung Barat
* **Framework:** Laravel 11/13
* **Database:** MySQL / MariaDB (`db_lembangfresh`) & SQLite
* **Peta Spasial:** Geographic Information System (GIS) menggunakan Leaflet.js & OpenStreetMap

---

## BAB I: PENDAHULUAN & LATAR BELAKANG

### 1.1 Deskripsi Sistem
LembangFresh merupakan sistem informasi berbasis Geographic Information System (GIS) yang dirancang untuk membantu para petani di Kecamatan Lembang dalam memasarkan dan mendistribusikan hasil panen hortikultura secara efisien dan transparan. Sistem ini tidak sekadar berfungsi sebagai pasar komoditas digital (*marketplace*), tetapi juga mengintegrasikan penentuan jalur pemasaran berbasis kualitas hasil panen (*quality-based marketing*), pencocokan pasokan dan permintaan (*supply-demand matching*), serta koordinasi logistik pengiriman lokal yang fleksibel.

### 1.2 Rumusan Masalah yang Diselesaikan
1. **Fluktuasi Pasokan & Permintaan:** Petani sering kali mengalami kelebihan pasokan (*over-supply*) jika hanya mengandalkan satu tengkulak/pembeli. LembangFresh membantu mencari pembeli lain melalui pencocokan permintaan (*demand*).
2. **Kualitas Panen Berbeda-beda:** Hasil panen memiliki mutu yang beragam. Sistem membagi produk ke dalam tiga tingkatan mutu (*Grade A, Grade B, Grade C*) dan secara otomatis mengarahkan ke jalur pasar yang sesuai.
3. **Disparitas Lokasi & Hambatan Distribusi:** Petani dan pembeli berada pada titik geografis yang tersebar di perbukitan Lembang. Sistem memanfaatkan GIS untuk menghitung jarak tempuh riil dan menyediakan tiga opsi distribusi (Ambil Sendiri, Diantar Petani, atau Diantar Mitra Pickup).

---

## BAB II: ARSITEKTUR SISTEM & IMPLEMENTASI HMVC

### 2.1 Arsitektur Hierarchical Model-View-Controller (HMVC)
Aplikasi memisahkan domain fungsional ke dalam modul mandiri di bawah direktori `app/Modules/`, yang didaftarkan secara otomatis melalui `App\Providers\ModuleServiceProvider`:

```
app/
├── Modules/
│   ├── Harvest/                       # Modul 1: Hasil Panen (Synchronous CRUD)
│   │   ├── Controllers/
│   │   │   └── HarvestController.php  # Resource Controller
│   │   ├── Models/
│   │   │   └── Harvest.php            # Model Komoditas Panen
│   │   ├── Requests/
│   │   │   ├── StoreHarvestRequest.php
│   │   │   └── UpdateHarvestRequest.php
│   │   ├── Routes/
│   │   │   └── web.php                # Resource Route
│   │   └── Views/                     # Blade Views Modul (index, create, edit, show)
│   │
│   └── Logistics/                     # Modul 2: Logistik & Distribusi (AJAX CRUD)
│       ├── Controllers/
│       │   └── LogisticsAjaxController.php # JSON & FormData AJAX Controller
│       ├── Models/
│       │   └── LogisticsShipment.php  # Model Penugasan Pengiriman
│       ├── Routes/
│       │   └── web.php                # Rute API & Tampilan AJAX
│       └── Views/
│           └── index.blade.php        # Antarmuka SPA AJAX dengan Modal & File Viewer
```

---

## BAB III: PERANCANGAN BASIS DATA & MYSQL

### 3.1 Basis Data: `db_lembangfresh`
Basis data MySQL dinamai **`db_lembangfresh`** dan berkas skema dump SQL tersimpan di **`db_lembangfresh.sql`**.

### 3.2 Struktur Entitas Utama:
1. **Tabel `users`:**
   * Kolom: `id`, `name`, `email`, `password` (Bcrypt Hash), `role` (`admin`, `petani`, `pembeli`, `mitra`), `phone`, `address`, `lat`, `lng`, `is_verified`, `delivery_available`, `delivery_radius`, `delivery_fee`, `vehicle_desc`, `vehicle_capacity`, `is_active`, `timestamps`.
2. **Tabel `harvests`:**
   * Kolom: `id`, `farmer_id` (FK `users`), `commodity`, `variety`, `quantity` (kg), `price_per_kg`, `grade` (A/B/C), `marketing_channel`, `harvest_date`, `ready_date`, `photo_path` (Upload berkas), `description`, `status`, `timestamps`.
3. **Tabel `logistics_shipments`:**
   * Kolom: `id`, `tracking_code`, `mitra_id` (FK `users`, nullable), `sender_name`, `receiver_name`, `commodity_info`, `weight_kg`, `distance_km`, `shipping_method`, `shipping_cost`, `status`, `document_path` (Upload berkas surat jalan), `notes`, `timestamps`.
4. **Tabel `demands`:**
   * Kolom: `id`, `buyer_id` (FK `users`), `commodity`, `quantity`, `grade`, `max_price_per_kg`, `notes`, `document_path`, `status`, `timestamps`.
5. **Tabel `orders`:**
   * Kolom: `id`, `order_number`, `buyer_id`, `harvest_id`, `mitra_id`, `quantity`, `unit_price`, `delivery_method`, `shipping_cost`, `total_price`, `status`, `proof_path`, `timestamps`.

---

## BAB IV: PENERAPAN BLADE TEMPLATE & KOMPONEN

### 4.1 Master Layout (`layouts/app.blade.php`)
* Mewarisi struktur HTML5, konfigurasi meta CSRF token, font Google Plus Jakarta Sans, Bootstrap 5.3, FontAwesome 6, dan Leaflet GIS CSS/JS.
* Memuat partial bilah navigasi (`@include('partials.navbar')`) dan kaki halaman (`@include('partials.footer')`).
* Menyediakan slot konten dinamis via `@yield('content')` serta `@stack('styles')` dan `@stack('scripts')`.

### 4.2 Directive Blade
* **Percabangan (`@if`, `@elseif`, `@else`, `@empty`, `@auth`, `@guest`):** Digunakan untuk merender kontrol navigasi sesuai status login/guest, menampilkan pesan alert validasi, menentukan warna badge tingkatan mutu komoditas, dan proteksi tombol aksi.
* **Perulangan (`@foreach`, `@forelse`):** Digunakan untuk merender grid katalog kartu produk hasil panen, marker titik spasial GIS di Leaflet, serta tabel riwayat transaksi dan penugasan armada.

### 4.3 Komponen & Partial Blade
1. `<x-status-badge :status="..." :type="..." />`: Menampilkan badge dinamis untuk tingkatan mutu (Grade A hijau, Grade B kuning, Grade C abu-abu), peran pengguna, status verifikasi, dan status alur pesanan.
2. `<x-alert />`: Menampilkan notifikasi sukses (*flash message*) dan daftar galat validasi formulir secara konsisten.
3. `<x-product-card :harvest="..." />`: Menampilkan kartu komoditas panen lengkap dengan pratinjau gambar, nama petani, harga/kg, jarak GIS, dan jalur pemasaran.
4. `@include('partials.navbar')`: Bilah navigasi cerdas yang menyesuaikan menu berdasarkan status autentikasi dan hak akses role.
5. `@include('partials.footer')`: Kaki halaman resmi LembangFresh.

### 4.4 Named Routes
Seluruh rute sistem dipanggil menggunakan helper `route('...')` tanpa hardcoded URL, misalnya: `route('home')`, `route('login')`, `route('dashboard')`, `route('harvests.index')`, `route('harvests.create')`, `route('harvests.store')`, `route('harvests.show', $id)`, `route('harvests.edit', $id)`, `route('harvests.update', $id)`, `route('harvests.destroy', $id)`, `route('logistics.index')`, `route('gis.index')`, dan `route('matching.index')`.

---

## BAB V: FITUR AUTENTIKASI & OTORISASI BERBASIS ROLE

### 5.1 Alasan Pemilihan Autentikasi Manual Terstruktur
> **Penjelasan Pemilihan untuk Laporan:**  
> Alih-alih menggunakan scaffolding pihak ketiga seperti Breeze yang sering kali mengunci struktur antarmuka dan menimbulkan konflik ketergantungan library pada lingkungan evaluasi luring, implementasi autentikasi dibangun secara kustom dan terstruktur (`AuthController`). Pendekatan ini memungkinkan:
> 1. Penanganan multi-aktor secara langsung sejak form registrasi (Petani, Pembeli, Mitra Logistik).
> 2. Validasi form dengan pesan galat berbahasa Indonesia yang jelas dan ramah pengguna.
> 3. Penyimpanan kata sandi terenkripsi menggunakan algoritma **Bcrypt** standar industri (`Hash::make`).
> 4. Pengelolaan sesi yang bersih dan proteksi pencegahan serangan CSRF (*Cross-Site Request Forgery*).

### 5.2 Akun Dummy Menggunakan Seeder & Faker
Didaftarkan pada `database/seeders/UserSeeder.php` dan dipanggil di `DatabaseSeeder.php`:
* **5 Aktor Utama LembangFresh:**
  1. *Admin:* `admin@lembangfresh.test` (Sandi: `admin123`)
  2. *Petani 1 (Agus):* `agus@lembangfresh.test` (Sandi: `petani123`, Desa Cikidang, antar aktif radius 5 km)
  3. *Petani 2 (Siti):* `siti@lembangfresh.test` (Sandi: `petani123`, Desa Cibodas, antar nonaktif)
  4. *Pembeli (Salad Fresh):* `salad@lembangfresh.test` (Sandi: `pembeli123`, UMKM Jl. Panorama)
  5. *Mitra (Budi):* `budi@lembangfresh.test` (Sandi: `mitra123`, Armada Pickup L300 D 1234 XY)
* **5 Akun Dummy Tambahan Hasil Faker:**
  * Dihasilkan secara otomatis menggunakan `Faker\Factory::create('id_ID')` dengan sebaran nama Indonesia, alamat riil perdesaan Lembang (*Cikahuripan, Jayagiri, Kayuambon, dll*), kata sandi ter-hash `Hash::make('password')`, dan variasi peran pengguna.

### 5.3 Otorisasi Middleware (`CheckRole`)
Dibuat middleware `App\Http\Middleware\CheckRole` dan didaftarkan dengan alias `role` pada `bootstrap/app.php`:
* `role:admin,petani`: Mengamankan akses pengelolaan data panen.
* `role:admin,mitra`: Mengamankan akses modul logistik dan surat jalan.
* Seluruh upaya akses silang yang tidak berhak menghasilkan respons HTTP `403 Forbidden`, sedangkan tamu yang belum terautentikasi otomatis diarahkan ke halaman login (`302 Redirect`).

---

## BAB VI: IMPLEMENTASI CRUD SYNCHRONOUS & UPLOAD BERKAS

### 6.1 Alur dan Controller
* Dikelola melalui Resource Controller `App\Modules\Harvest\Controllers\HarvestController`.
* **Create (Simpan Baru):** Memvalidasi komoditas, varietas, stok, harga, grade (A/B/C), tanggal panen/kirim, dan berkas foto. File gambar diunggah ke `storage/app/public/harvests/` dan path disimpan ke kolom `photo_path`.
* **Read (Tampil Data):** Menampilkan galeri komoditas dengan paginasi, pencarian, dan filter tingkatan mutu.
* **Update (Perbarui Data):** Jika pengguna mengunggah foto baru pengganti, sistem secara otomatis menghapus berkas foto lama dari storage menggunakan `Storage::disk('public')->delete(...)`.
* **Delete (Hapus Data):** Saat data komoditas dihapus, berkas foto fisik pada disk storage otomatis terhapus bersama baris tabel database.
* **Rekomendasi Jalur Pemasaran:**
  * *Grade A:* Pasar Normal (Supermarket, Hotel, Restoran).
  * *Grade B:* Pasar Alternatif (UMKM Kuliner, Rumah Makan, Katering).
  * *Grade C:* Jalur Pengolahan (Industri Saus, Sambal, Pasta, Keripik).

---

## BAB VII: IMPLEMENTASI CRUD AJAX & UNGGAH BERKAS SURAT JALAN

### 7.1 Alur dan Controller
* Dikelola melalui `App\Modules\Logistics\Controllers\LogisticsAjaxController`.
* **Create via AJAX:** Mengirimkan data via objek JavaScript `FormData` ke endpoint `POST /logistics/api/store` lengkap dengan berkas surat jalan/bukti serah terima armada. Menghasilkan respons JSON status `201 Created` dan menyisipkan baris tabel baru ke DOM tanpa reload halaman.
* **Read via AJAX:** Mengambil data penugasan secara real-time melalui endpoint `GET /logistics/api/data` yang merender ulang tabel saat filter status atau kolom pencarian diketik.
* **Update via AJAX:** Membuka data ke modal via `GET /logistics/api/{id}` dan menyimpan pembaruan via `POST /logistics/api/{id}/update`. Berkas lama digantikan secara otomatis pada storage jika ada berkas baru yang diunggah.
* **Delete via AJAX:** Menghapus data dan menghapus berkas dari storage via `DELETE /logistics/api/{id}` dengan pembaruan instan pada tabel DOM.

### 7.2 Fitur Wajib: Buka Kembali Berkas yang Diunggah
* Setiap baris data memiliki tombol **"Buka Berkas"** yang membuka modal pratinjau dokumen (*image preview* atau *embedded PDF viewer*).
* Menyediakan tautan **"Buka Berkas di Tab Baru"** (`target="_blank"`) dan tombol **"Unduh Berkas"** yang mengalirkan file langsung melalui endpoint [`/logistics/{id}/download`](file:///Users/denseaikartina/Documents/Kelompok/Tubes/app/Modules/Logistics/Controllers/LogisticsAjaxController.php#L198-L208).

---

## BAB VIII: FITUR GIS SPASIAL & SUPPLY-DEMAND MATCHING

### 8.1 Peta GIS Kecamatan Lembang (`resources/views/gis.blade.php`)
* Menampilkan titik koordinat kebun petani, pembeli, dan mitra armada di Kecamatan Lembang.
* **Formula Jarak Haversine:** Menghitung jarak tempuh lengkung bumi akurat antara dua titik koordinat:
  $$d = 2R \cdot \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta \text{lat}}{2}\right) + \cos(\text{lat}_1)\cos(\text{lat}_2)\sin^2\left(\frac{\Delta \text{lng}}{2}\right)}\right)$$
* **Kalkulasi Biaya Distribusi Sesuai SDD Bab 7:**
  1. *Ambil Sendiri:* Rp 0 (Gratis).
  2. *Diantar Petani:* Tarif tetap petani jika lokasi pembeli masih berada di dalam radius jangkauan antar petani.
  3. *Mitra Pengangkut:* Tarif dasar Rp 50.000 + ($\lceil \text{Jarak km} \rceil \times \text{Rp } 5.000$).
* **Simulator Interaktif:** Pengguna dapat memilih petani dan pembeli untuk langsung melihat simulasi rute garis putus-putus pada peta GIS beserta perhitungan ongkos kirim.

### 8.2 Supply-Demand Matching Engine (`resources/views/matching.blade.php`)
* Menganalisis kebutuhan terbuka pembeli dan mencocokkannya dengan stok hasil panen petani berdasarkan:
  1. Kesamaan nama komoditas.
  2. Kesesuaian tingkatan grade mutu (A/B/C).
  3. Batas harga maksimal pembelian per kg.
  4. Perhitungan jarak spasial GIS antara lokasi kebun petani dan alamat pembeli.
* Menampilkan status penyerapan pasar (*absorpsi 100% atau sisa stok tetap tersedia untuk pembeli lain*).
* Menampilkan evaluasi ketersediaan pengantaran langsung oleh petani berdasarkan batas radius jelajah (*delivery radius*).

### 8.3 Enam Pembeda Utama LembangFresh (Sesuai SDD Bab 14)
1. **Quality-Based Marketing:** Pengelompokan jalur distribusi mutu (Grade A untuk pasar normal/supermarket, Grade B untuk UMKM/rumah makan, Grade C untuk industri olahan).
2. **Supply-Demand Matching:** Penyerapan kuantitas panen secara proporsional untuk mencegah petani merugi akibat *oversupply*.
3. **GIS-Based Distribution:** Pemetaan sebaran spasial dan penghitungan jarak tempuh akurat di wilayah perbukitan Kecamatan Lembang.
4. **Fleksibilitas 3 Jalur Pengiriman:** Pilihan metode ambil sendiri (Rp 0), antar langsung petani (dalam radius), atau mitra pengangkut pickup lokal.
5. **Farmer-Controlled Delivery:** Petani memegang kendali penuh dalam mengaktifkan layanan antar, menentukan batas radius (km), dan tarif tetap.
6. **Penyerapan Pasar Alternatif:** Mengamankan nilai ekonomis hasil panen berlebih maupun kualitas non-standar ke sektor kuliner dan industri olahan pangan lokal.

---

## BAB IX: BUKTI PENGUJIAN & LAMPIRAN EVALUASI

### 9.1 Bukti Basis Data phpMyAdmin
1. **Nama Basis Data:** `db_lembangfresh`
2. **Pemeriksaan Hash Sandi:** Buka tabel `users`, seluruh nilai kolom `password` tersimpan dalam format hash Bcrypt 60 karakter (`$2y$12$...`).
3. **Pemeriksaan Path Berkas:**
   * Kolom `harvests.photo_path`: Memuat path relatif penyimpanan gambar (misal: `harvests/wortel.png`).
   * Kolom `logistics_shipments.document_path`: Memuat path berkas surat jalan (misal: `logistics/surat_jalan_001.png`).
4. **Pemeriksaan Folder Storage:** Berkas fisik tersimpan pada `storage/app/public/harvests/` dan `storage/app/public/logistics/` serta terhubung dengan symlink `public/storage`.

### 9.2 Hasil Pengujian Otomatis (PHPUnit / Pest Test Suite)
Seluruh 10 pengujian fitur pada [`tests/Feature/LembangFreshTest.php`](file:///Users/denseaikartina/Documents/Kelompok/Tubes/tests/Feature/LembangFreshTest.php) berhasil dilewati dengan **status lulus 100% (82 assertions passed)**:
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

