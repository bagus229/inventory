# CodeIgniter 4 Framework

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds the distributable version of the framework.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Contributing

We welcome contributions from the community.

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

## 1. Struktur Folder

```
backend-ci4/
├── app/
│   ├── Config/
│   │   ├── App.php
│   │   ├── Database.php
│   │   ├── Filters.php          <-- registrasi global CORS + AuthFilter per-method
│   │   └── Routes.php           <-- definisi semua endpoint API
│   ├── Controllers/
│   │   └── Api/
│   │       ├── BaseApiController.php
│   │       ├── AuthController.php
│   │       ├── KategoriController.php
│   │       ├── SupplierController.php
│   │       ├── BarangController.php
│   │       ├── HistoriController.php
│   │       └── DashboardController.php
│   ├── Database/
│   │   ├── Migrations/          <-- 5 file migration (users, kategori, supplier, barang, histori)
│   │   └── Seeds/
│   │       └── DatabaseSeeder.php
│   ├── Filters/
│   │   ├── CorsFilter.php       <-- handle CORS + preflight OPTIONS
│   │   └── AuthFilter.php       <-- validasi Bearer Token JWT
│   └── Models/
│       ├── UserModel.php
│       ├── KategoriModel.php
│       ├── SupplierModel.php
│       ├── BarangModel.php
│       └── HistoriModel.php
├── public/
│   ├── index.php
│   └── .htaccess
├── composer.json
└── env                          <-- copy/rename jadi .env
```

## 2. Relasi Database (ERD Ringkas)

```
users (id, nama, username, password, role)
   │
   │ 1‑‑‑‑* (siapa yang transaksi)
   ▼
histori_transaksi (id, barang_id*, user_id*, jenis_transaksi, jumlah, keterangan)
   ▲
   │ *‑‑‑‑1
   │
barang (id, kategori_id*, supplier_id*, kode_barang, nama_barang, harga_beli, harga_jual, stok, satuan)
   ▲                              ▲
   │ *‑‑‑‑1                       │ *‑‑‑‑1
   │                              │
kategori (id, nama_kategori)   supplier (id, nama_supplier, alamat, telepon, email)
```

- **barang.kategori_id** → FK ke `kategori.id`
- **barang.supplier_id** → FK ke `supplier.id`
- **histori_transaksi.barang_id** → FK ke `barang.id` (CASCADE saat barang dihapus)
- **histori_transaksi.user_id** → FK ke `users.id`

Total **5 tabel** berelasi (lebih dari syarat minimal 3 tabel).

## 3. Instalasi & Setup

```bash
# 1. Install dependency (CodeIgniter 4 + firebase/php-jwt untuk Bearer Token)
composer install

# 2. Copy file env -> .env
cp env .env

# 3. Edit .env: sesuaikan kredensial database & JWT_SECRET_KEY
#    database.default.database = db_inventory
#    JWT_SECRET_KEY = 'ganti-dengan-string-acak-yang-panjang'

# 4. Buat database kosong "db_inventory" di MySQL, lalu jalankan migrasi:
php spark migrate

# 5. Isi data awal (user admin/staff, kategori, supplier, barang contoh):
php spark db:seed DatabaseSeeder

# 6. Jalankan server development:
php spark serve
# API akan berjalan di http://localhost:8080
```

**Akun default hasil seeder:**
| Username | Password  | Role  |
|----------|-----------|-------|
| admin    | admin123  | admin |
| staff    | staff123  | staff |

## 4. Daftar Endpoint API

### Auth (Publik, tanpa token — kecuali /me & /logout)
| Method | Endpoint        | Keterangan                          |
|--------|-----------------|--------------------------------------|
| POST   | /api/login      | Login, mengembalikan JWT token       |
| POST   | /api/register   | Registrasi user baru                 |
| GET    | /api/me         | Data user login (butuh token)        |
| POST   | /api/logout     | Logout (butuh token)                 |

### Kategori (Resource Controller)
| Method | Endpoint            | Token? | Keterangan        |
|--------|---------------------|--------|--------------------|
| GET    | /api/kategori       | ❌     | List semua kategori |
| GET    | /api/kategori/{id}  | ❌     | Detail kategori     |
| POST   | /api/kategori       | ✅     | Tambah kategori     |
| PUT    | /api/kategori/{id}  | ✅     | Update kategori     |
| DELETE | /api/kategori/{id}  | ✅     | Hapus kategori      |

### Supplier (Resource Controller) — pola endpoint sama seperti Kategori
`GET/POST/PUT/DELETE /api/supplier[/{id}]`

### Barang (Resource Controller, relasi ke Kategori & Supplier)
`GET/POST/PUT/DELETE /api/barang[/{id}]`
- Response GET otomatis JOIN dan menyertakan `nama_kategori` & `nama_supplier`.

### Histori Transaksi (Resource Controller, relasi ke Barang & Users)
`GET/POST/DELETE /api/histori[/{id}]`
- POST otomatis mengubah `stok` pada tabel `barang` (transaksional).
- DELETE otomatis mengembalikan (rollback) stok barang.

### Dashboard
| Method | Endpoint                | Keterangan                                  |
|--------|--------------------------|----------------------------------------------|
| GET    | /api/dashboard/summary   | Total barang/kategori/supplier, stok menipis, transaksi terbaru |

## 5. Cara Pakai Token (Bearer Authorization)

1. Login dulu:
```bash
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```
Response akan berisi `data.token`.

2. Pakai token tersebut di header setiap request POST/PUT/DELETE:
```bash
curl -X POST http://localhost:8080/api/kategori \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <TOKEN_DISINI>" \
  -d '{"nama_kategori":"Makanan","deskripsi":"Kategori makanan"}'
```

3. Jika token tidak disertakan / salah / expired, API membalas:
```json
{
  "status": false,
  "message": "Header Authorization Bearer Token tidak ditemukan."
}
```
dengan HTTP status `401 Unauthorized`.

## 6. CORS untuk Frontend SPA

`CorsFilter` (di `app/Filters/CorsFilter.php`) didaftarkan **global** lewat
`Config/Filters.php` (`$globals['before'] = ['cors']`), sehingga:

- Semua origin di whitelist (`http://localhost:5173`, dst — sesuaikan dengan
  port dev server Vite/React kamu) bisa mengakses API ini.
- Request preflight `OPTIONS` otomatis dijawab `204 No Content` dengan
  header CORS lengkap, tanpa menyentuh controller.
- Header yang diizinkan: `Content-Type`, `Authorization`, dll, sehingga
  frontend bebas mengirim JSON + Bearer Token tanpa diblokir browser.

**Penting:** Edit array `$allowedOrigins` di `CorsFilter.php` sesuai dengan
URL frontend SPA kamu (misalnya `http://localhost:5173` untuk Vite default).

## 7. Integrasi dengan Frontend (folder `frontend-examples/`)

Berisi contoh konkret untuk struktur frontend yang kamu punya:
- `apiClient.js` → axios instance + auto-attach Bearer Token (untuk `pages/`)
- `Login.js` → contoh integrasi ke `/api/login`
- `Barang.js` → contoh CRUD penuh ke `/api/barang`

Tinggal sesuaikan `baseURL` di `apiClient.js` dan letakkan di
`frontend-spa/src/api/apiClient.js`, lalu import di tiap halaman
(`Barang.js`, `Kategori.js`, `Supplier.js`, `Histori.js`, `Dashboard.js`).
=======
# UAS_Web2_312410382_Bagus_Aditya_Hermawan

# Judul: Sistem Manajemen Inventaris Barang (E-Inventory) Berbasis Vue.js dan CodeIgniter 4.

## Deskripsi Singkat: 
> E-Inventory merupakan sistem informasi berbasis web yang dirancang untuk membantu proses pengelolaan persediaan barang secara terintegrasi. Sistem ini menyediakan fitur manajemen data kategori, supplier, barang, serta pencatatan histori transaksi keluar dan masuk barang. Aplikasi dibangun menggunakan Vue.js dengan konsep Single Page Application (SPA) dan CodeIgniter 4 yang menyediakan layanan RESTfull API. Sistem menerapkan hak akses yang berbeda antara public dan admin, di mana pengunjung hanya dapat melihat halaman beranda (landing page), sedangkan admin dapat melakukan login untuk mengelola seluruh data inventaris. Dengan adanya sistem ini, proses pengelolaan stok menjadi lebih terstruktur, efisien, dan terintegrasi.

## Spesifikasi Teknologi Yang Digunakan
- Backend Engine: Codelingniter 4
- Frontend Engine: Vue.js 3, Vue Router
- UI: TailwindCSS
- Data Transfer: Library Axios dan MySQL

## Fitur Aplikasi
Backend: 
- Relasi Tabel
- RESTfull Endpoints
- Server-Side Security
- Penanganan CORS

Frontend:
- Sistem Login
- Client-Side Security
- Axios Interceptors
- TailwindCSS

Hak Akses
- Pengunjung: halaman beranda
- Administrator: Mengelola data master dan aktivitas logout.

## Dokumentasi Database dan Uji Coba
Skema relasi tabel databse
##### ![Gambar 1](ss2/gambar1.png).

Uji tembak API gagal (error 401)
##### ![Gambar 1](ss2/gambar2.png).

## Dokumentasi Antarimuka Aplikasi (UI)
### 1. Halaman login
##### ![Gambar 1](ss2/gambar3.png).

### 2. Halaman Home dan dashboard admin
##### ![Gambar 1](ss2/gambar4.png).
##### ![Gambar 1](ss2/gambar18.png).

### 3. Halaman Home pengunjung
##### ![Gambar 1](ss2/gambar5.png).
### 4. Form modal
Modal tambah:
- Barang dan Stok
##### ![Gambar 1](ss2/gambar6.png).
- Supplier
##### ![Gambar 1](ss2/gambar7.png).
- Histori
##### ![Gambar 1](ss2/gambar8.png).
- Kategori
##### ![Gambar 1](ss2/gambar9.png).

Modal Edit:
- Barang dan Stok
##### ![Gambar 1](ss2/gambar10.png).
- Supplier
##### ![Gambar 1](ss2/gambar11.png).
- Histori
##### ![Gambar 1](ss2/gambar12.png).
- Kategori
##### ![Gambar 1](ss2/gambar13.png).

### 5. Tabel data
- Barang dan Stok
##### ![Gambar 1](ss2/gambar14.png).
- Supplier
##### ![Gambar 1](ss2/gambar15.png).
- Histori
##### ![Gambar 1](ss2/gambar16.png).
- Kategori
##### ![Gambar 1](ss2/gambar17.png).



## Petunjuk Instalasi — E-Inventory (Backend & Frontend) 

#### Menyiapkan Database

1. Pertama-tama Buka XAMPP Control Panel, lalu start Apache dan MySQL. Agar nantinya ketika di coba dibrowser dapat berjalan.
2. Membuka halaman admin di phpMyadmin pada MySQL.
3. Kemudian saya membuat database dengan nama db_inventory.
4. Mengimport file SQL struktur tabel (`kategori`, `barang`, `supplier`, `histori_barang`, `users`) ke database.

### 2. Menjalankan Backend (CodeIgniter 4)
1. Membuka shell pada Xampp lalu, jalankan perintah berikut:
   ```
   php spark serve
   ```
2. Membuka file `.env` di folder backend, lalu koneksi database yang telah dibuat hubungkan pada file ini:
   ```
   CI_ENVIRONMENT = development

   database.default.hostname = localhost
   database.default.database = db_inventory
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```
3. Backend dapat diakses melalui Apache.
   ```
   http://localhost:8080/
   ```
4. Kemudian Uji coba backend berjalan dengan menembak pada postman:
   ```
   http://localhost:8080/api/dashboard-summary
   ```
   Dengan Method GET. Hasilnya jika berhasil akan muncul response JSON yg berisi data.

### 3. Menjalankan Frontend (Vue 3 via CDN)

1. Membuka folder project frontend.
   ```
   C:\xampp\htdocs\inventory\frontend-spa
   ```
2. Membuat `apiUrl` di file app.js frontend menunjuk ke alamat backend yang sudah dibuat:
   ```javascript
   const apiUrl = 'http://localhost:8080';
   ```
3. Uji coba menggunakan Xampp dengan mengakses frontend melalui browser.
untuk admin:
   ```
   http://localhost/inventory/frontend-spa/
   ```
untuk public:
   ```
   http://localhost/inventory/frontend-spa/public.html#/
   ```

### 4. Login ke Sistem

Menggunakan akun admin yang sudah dibuat pada database.
- username: admin@inventory.com.
- password: admin123.

Link demo: https://uas-web2-312410382-bagus-aditya-her.vercel.app/#/.
Link presentasi proyek: https://youtu.be/nfqkbWNG-fY.
