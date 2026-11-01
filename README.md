# Lokalku — Aplikasi E-Commerce UMKM Lokal

Aplikasi e-commerce untuk produk UMKM lokal yang dibangun dengan **Laravel** dan **Tailwind CSS**. Mendukung dua peran pengguna — **pembeli (buyer)** dan **penjual (seller)** — lengkap dengan alur onboarding, keranjang, checkout, pembayaran, manajemen toko, hingga terminal **Kasir POS**.

> Repository: <https://github.com/faridraka/e-commerce.git>

---

## Daftar Isi

- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi — Docker (Disarankan)](#instalasi--docker-disarankan)
- [Instalasi — PHP Native (Tanpa Docker)](#instalasi--php-native-tanpa-docker)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Akun & Data Demo](#akun--data-demo)
- [Alur Onboarding](#alur-onboarding)
- [Daftar Rute & API](#daftar-rute--api)
- [Struktur Proyek](#struktur-proyek)
- [Testing](#testing)
- [Catatan Keamanan](#catatan-keamanan)
- [Troubleshooting](#troubleshooting)
- [Lisensi](#lisensi)

---

## Fitur

**Pembeli (Buyer)**

- Katalog produk dan halaman detail produk (publik)
- Registrasi, login, dan onboarding pemilihan peran
- Keranjang belanja (tambah, ubah jumlah, hapus, kosongkan)
- Checkout dengan alamat pengiriman (multi-alamat + alamat utama)
- Riwayat pesanan dan halaman pembayaran (konfirmasi transfer)
- Pengelolaan profil dan alamat

**Penjual (Seller)**

- Onboarding khusus penjual: daftar toko baru atau verifikasi dengan **kode aktivasi toko**
- Dasbor penjual (khusus toko terverifikasi)
- Manajemen kategori dan produk (CRUD, aktif/nonaktif)
- Penyesuaian stok dengan pencatatan pergerakan stok (*stock movements*)
- Kelola pesanan online dan pembaruan status
- Terminal **POS (Point of Sale)**: checkout kasir, struk, dan riwayat transaksi

**Lain-lain**

- API JSON publik untuk produk
- Redirect permanen (301/307) dari URL lama berbahasa Indonesia ke URL baru berbahasa Inggris
- Middleware peran: `EnsureBuyer`, `EnsureSeller`, `EnsureOnboardingCompleted`, `RedirectSellerFromStorefront`

---

## Teknologi

| Komponen        | Versi / Teknologi                          |
|-----------------|--------------------------------------------|
| PHP             | ^8.3 (image Docker: PHP 8.4.27 FPM Alpine) |
| Laravel         | ^13.17                                     |
| Database        | MySQL 8.4 (Docker) / SQLite (default lokal)|
| Frontend        | Tailwind CSS v4 + Vite 8                   |
| Web server      | Nginx (stable-alpine)                      |
| Tools DB        | phpMyAdmin                                 |
| Testing         | PHPUnit ^12.5                              |

---

## Persyaratan Sistem

**Jalur Docker:**

- Docker Engine 24+ dan Docker Compose v2

**Jalur PHP native:**

- PHP >= 8.3 dengan ekstensi: `pdo_mysql` (atau `pdo_sqlite`), `mbstring`, `zip`, `gd`, `intl`, `bcmath`, `exif`
- Composer 2
- Node.js 20+ dan npm

---

## Instalasi — Docker (Disarankan)

Layanan yang dijalankan:

| Layanan       | Container         | Port (host)        | Keterangan                          |
|---------------|-------------------|--------------------|-------------------------------------|
| `app`         | ecommerce-app     | — (internal 9000)  | PHP-FPM 8.4, auto-setup saat start  |
| `web`         | ecommerce-web     | `127.0.0.1:8000`   | Nginx → aplikasi Laravel            |
| `db`          | ecommerce-db      | `127.0.0.1:3306`   | MySQL 8.4                           |
| `phpmyadmin`  | ecommerce-pma     | `127.0.0.1:8080`   | Administrasi database               |
| `node`        | ecommerce-node    | `5173`             | Vite dev server (profil `dev` saja) |

### Langkah-langkah

```bash
# 1. Clone repository
git clone https://github.com/faridraka/e-commerce.git
cd e-commerce

# 2. Buat network eksternal untuk reverse proxy (dipakai service "web")
docker network create web

# 3. (Opsional) siapkan file .env — container app akan membuat otomatis jika belum ada
cp .env.example .env

# 4. Build & jalankan semua layanan
docker compose up -d --build --pull always
```

Saat pertama kali start, container `app` menjalankan `docker/php/init.sh` yang otomatis:

1. Menginstal dependency Composer (`composer install`) + menjalankan audit keamanan
2. Membuat file `.env` dari `.env.example` dan mengatur koneksi MySQL ke service `db`
3. Membuat `APP_KEY` jika belum ada
4. Menunggu database siap, lalu menjalankan migrasi (`RUN_MIGRATIONS=true` secara default)
5. Membuat symbolic link storage (`php artisan storage:link`)

### Menjalankan aset frontend (dev)

```bash
# Profil "dev" menjalankan container node untuk Vite + Tailwind
docker compose --profile dev up -d
```

Atau build aset produksi sekali jalan:

```bash
docker compose exec app npm install
docker compose exec app npm run build
```

### Selesai

- Aplikasi: <http://localhost:8000>
- phpMyAdmin: <http://localhost:8080> (server: `db`, user: `ecommerce`, password: `ecommerce123`)

### Perintah Artisan via Docker

```bash
docker compose exec app php artisan tinker
docker compose exec app php artisan migrate:fresh --seed   # reset + data demo
docker compose exec app composer test                       # jalankan test suite
```

### Menghentikan

```bash
docker compose down          # hentikan container (data MySQL tetap ada di volume dbdata)
docker compose down -v       # hentikan + hapus volume database
```

---

## Instalasi — PHP Native (Tanpa Docker)

### Opsi A: Cepat (SQLite, satu perintah)

Project ini menyediakan script `composer setup` yang menangani semuanya:

```bash
git clone https://github.com/faridraka/e-commerce.git
cd e-commerce
composer run setup
```

Script tersebut menjalankan: `composer install` → salin `.env.example` ke `.env` → `php artisan key:generate` → `php artisan migrate` → `npm install` → `npm run build`.

> `.env.example` default memakai `DB_CONNECTION=sqlite`. Pastikan file `database/database.sqlite` ada (sudah tersedia di repo, atau buat dengan `touch database/database.sqlite`).

### Opsi B: Manual dengan MySQL

```bash
# 1. Clone & masuk folder
git clone https://github.com/faridraka/e-commerce.git
cd e-commerce

# 2. Instal dependency
composer install
npm install

# 3. Siapkan environment
cp .env.example .env
php artisan key:generate

# 4. Buat database MySQL, lalu sesuaikan .env:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=ecommerce
#    DB_USERNAME=root
#    DB_PASSWORD=rahasia_anda

# 5. Migrasi + data demo
php artisan migrate --seed

# 6. Storage link (untuk file upload)
php artisan storage:link

# 7. Build aset frontend
npm run build

# 8. Jalankan aplikasi
php artisan serve
```

Aplikasi berjalan di <http://localhost:8000>.

### Mode pengembangan (hot reload)

```bash
# Terminal 1 — server Laravel
php artisan serve

# Terminal 2 — Vite dev server (Tailwind CSS)
npm run dev

# Terminal 3 (opsional) — pemantau queue/log realtime
php artisan pail
```

Atau jalankan semuanya dalam satu perintah:

```bash
composer dev
```

---

## Konfigurasi Environment

Konfigurasi utama di file `.env` (lihat `.env.example`):

| Variabel | Default | Keterangan |
|---|---|---|
| `APP_NAME` | `Laravel` | Nama aplikasi |
| `APP_ENV` | `local` | `local` / `production` (mempengaruhi `composer install` di Docker) |
| `APP_KEY` | *(kosong)* | Dibuat otomatis oleh `php artisan key:generate` |
| `APP_URL` | `http://localhost:8000` | URL dasar aplikasi |
| `DB_CONNECTION` | `sqlite` | Ganti ke `mysql` untuk Docker / MySQL |
| `DB_HOST` | `127.0.0.1` | Di Docker wajib `db` |
| `DB_PORT` | `3306` | Port MySQL |
| `DB_DATABASE` | — | Nama database |
| `DB_USERNAME` / `DB_PASSWORD` | — | Kredensial database |
| `SESSION_DRIVER` | `database` | Driver sesi |
| `QUEUE_CONNECTION` | `database` | Driver queue |
| `CACHE_STORE` | `database` | Driver cache |
| `MAIL_MAILER` | `log` | Email ditulis ke log (cocok untuk dev) |

**Variabel khusus Docker** (di-set di shell atau file `.env` host):

| Variabel | Default | Keterangan |
|---|---|---|
| `DOCKER_UID` / `DOCKER_GID` | `1000` / `1000` | UID/GID user non-root di container agar file tidak jadi milik root |
| `DB_DATABASE` | `ecommerce` | Nama database MySQL |
| `DB_USERNAME` | `ecommerce` | User MySQL |
| `DB_PASSWORD` | `ecommerce123` | Password MySQL |
| `DB_ROOT_PASSWORD` | `ecommerce123` | Password root MySQL |
| `RUN_MIGRATIONS` | `true` | Set `false` untuk menonaktifkan auto-migrate saat container start |

---

## Akun & Data Demo

Jalankan seeder untuk mengisi data demo:

```bash
php artisan migrate:fresh --seed
```

**Akun user:**

| Email | Password | Peran |
|---|---|---|
| `test@example.com` | `password` | Belum punya peran (lewat onboarding) |

> User hasil seeder belum memiliki peran — login lalu ikuti alur onboarding untuk memilih peran pembeli atau penjual.

**Toko demo (untuk verifikasi kode aktivasi penjual):**

| Nama Toko | Kode Aktivasi |
|---|---|
| Toko Kopi Gayo Aceh | `LKL-7294-9102` |
| Madu Hutan Sumbawa | `LKL-8821-4902` |
| Tenun Sasak Lombok | `LKL-3567-1208` |

Seeder juga mengisi kategori dan puluhan produk demo (kopi, madu, tenun, dll.) lengkap dengan harga, stok, rating, dan gambar.

---

## Alur Onboarding

1. Registrasi akun baru (`/register`)
2. Pilih peran di `/onboarding` — **Pembeli** atau **Penjual**
3. **Pembeli** → langsung diarahkan ke etalase
4. **Penjual** → pilih salah satu:
   - **Daftarkan toko baru** — sistem membuat toko dengan kode aktivasi unik, status belum terverifikasi
   - **Verifikasi toko** — masukkan kode aktivasi yang sudah ada (lihat tabel toko demo)
5. Penjual dengan toko **terverifikasi** dapat mengakses `/seller/dashboard` dan seluruh area penjual

Middleware `EnsureOnboardingCompleted` memastikan setiap user yang login telah menyelesaikan langkah di atas.

---

## Daftar Rute & API

### Rute publik

| Method | URI | Keterangan |
|---|---|---|
| GET | `/` | Etalase (homepage) |
| GET | `/products` | Katalog produk |
| GET | `/products/{slug}` | Detail produk |
| GET/POST | `/register`, `/login` | Autentikasi |
| POST | `/logout` | Keluar |

### API JSON publik

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/products` | Daftar produk (JSON) |
| GET | `/api/products/{slug}` | Detail produk (JSON) |

### Area pembeli (`auth` + onboarding + peran buyer)

| Method | URI | Keterangan |
|---|---|---|
| GET | `/cart` | Keranjang |
| POST | `/cart/{product}` | Tambah produk ke keranjang |
| PATCH/DELETE | `/cart/items/{cartItem}` | Ubah jumlah / hapus item |
| DELETE | `/cart` | Kosongkan keranjang |
| GET/POST | `/checkout` | Halaman & proses checkout |
| GET | `/orders` | Daftar pesanan |
| GET | `/orders/{order_code}` | Detail pesanan |
| GET/POST | `/orders/{order_code}/payment` | Halaman & konfirmasi pembayaran |
| GET/PATCH | `/profile` | Profil |
| CRUD | `/addresses` | Alamat + set alamat utama |

### Area penjual (prefix `/seller`, wajib toko terverifikasi)

| Method | URI | Keterangan |
|---|---|---|
| GET | `/seller/dashboard` | Dasbor |
| CRUD | `/seller/categories` | Kategori |
| CRUD | `/seller/products` | Produk + toggle aktif |
| GET/POST | `/seller/stock` | Penyesuaian stok |
| GET | `/seller/orders` | Pesanan online |
| POST | `/seller/orders/{order_code}/status` | Ubah status pesanan |
| GET | `/seller/pos` | Terminal kasir POS |
| POST | `/seller/pos/checkout` | Transaksi kasir |
| GET | `/seller/pos/history`, `/seller/pos/{order_code}/receipt` | Riwayat & struk |

### Redirect URL lama

URL lama berbahasa Indonesia (`/keranjang`, `/katalog`, `/produk/{slug}`, `/pesanan`, `/seller/dasbor`, dll.) diarahkan permanen ke URL baru berbahasa Inggris (301, atau 307 untuk method POST/PUT/PATCH/DELETE agar body tetap terkirim).

> Lihat daftar lengkap rute: `php artisan route:list`

---

## Struktur Proyek

```
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Controller pembeli, auth, dan penjual (namespace Seller\)
│   │   └── Middleware/         # EnsureBuyer, EnsureSeller, EnsureOnboardingCompleted, RedirectSellerFromStorefront
│   ├── Models/                 # User, Store, Product, Category, Cart, CartItem, Order, OrderItem, Payment, Address, StockMovement
│   └── Providers/
├── config/
├── database/
│   ├── factories/              # UserFactory, dll.
│   ├── migrations/             # users, stores, categories, products, carts, orders, payments, stock_movements, ...
│   └── seeders/                # StoreSeeder, CategorySeeder, ProductSeeder
├── docker/
│   └── php/
│       ├── Dockerfile          # PHP 8.4 FPM Alpine + ekstensi + hardening
│       ├── init.sh             # Auto-setup saat container start
│       └── nginx.conf          # Konfigurasi Nginx (blokir .env/.git, security headers)
├── resources/views/            # Blade templates (layouts, auth, seller, cart, orders, ...)
├── routes/
│   ├── web.php                 # Semua rute web + API + redirect
│   └── console.php
├── tests/
├── docker-compose.yaml
└── composer.json
```

---

## Testing

```bash
# Via Composer (membersihkan config lalu menjalankan PHPUnit)
composer test

# Atau langsung
php artisan test
```

---

## Catatan Keamanan

Konfigurasi default sudah memperhatikan keamanan:

- **Nginx**: `server_tokens off`, header `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, memblokir akses file tersembunyi (`.env`, `.git`), dan menyembunyikan `X-Powered-By`
- **PHP-FPM**: `expose_php=Off`, `display_errors=Off`, `log_errors=On`, batas upload 20M / POST 25M, OPcache aktif
- **Port hanya localhost**: MySQL (`3306`) dan phpMyAdmin (`8080`) di-bind ke `127.0.0.1`
- **Container non-root**: PHP-FPM berjalan sebagai user `app` dengan UID/GID mengikuti host
- Image PHP dipin ke `8.4.27` (rilis patch CVE) dan `init.sh` menjalankan `composer audit` setiap start

⚠️ **Penting untuk production:**

1. **Jangan pernah mengekspos phpMyAdmin ke publik** — layanan ini hanya untuk development
2. Set `APP_ENV=production` dan `APP_DEBUG=false`
3. Ganti semua password default (`ecommerce123`)
4. Aktifkan HTTPS di reverse proxy di depan network `web`

---

## Troubleshooting

**`network web declared as external, but could not be found`**
Buat network dulu: `docker network create web`

**Port 8000 / 8080 / 3306 sudah dipakai**
Ubah mapping port di `docker-compose.yaml`, misalnya `"8081:80"`.

**Container app berulang kali restart**
Periksa log: `docker compose logs -f app`. Penyebab umum: MySQL belum siap (tunggu healthcheck) atau `composer install` gagal karena jaringan.

**Migrasi tidak berjalan otomatis**
Set `RUN_MIGRATIONS=true` di environment, atau jalankan manual:
`docker compose exec app php artisan migrate --force`

**File hasil upload tidak bisa ditulis**
Pastikan `storage/` dan `bootstrap/cache/` writable, dan `DOCKER_UID`/`DOCKER_GID` sesuai user Anda (`id -u` dan `id -g` di Linux/macOS).

**Ganti kredensial database Docker**
Hapus volume agar MySQL dibuat ulang dengan kredensial baru:
`docker compose down -v && docker compose up -d --build`

---

## Lisensi

Proyek ini open-source dengan [lisensi MIT](https://opensource.org/licenses/MIT).
