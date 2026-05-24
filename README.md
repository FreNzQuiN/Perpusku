# Perpusku

Sistem Manajemen Perpustakaan berbasis web. Pengguna bisa daftar, login, cari buku, tambah ke keranjang, pinjam buku, dan lihat riwayat peminjaman.

Dibangun dengan **Laravel 12** + **Bootstrap 5** + **REST API**.

## Fitur

- 🔍 Cari buku berdasarkan judul
- 🛒 Keranjang pinjaman
- 📚 Peminjaman buku (maks 10 buku, maks 3 hari)
- 📖 Riwayat peminjaman per pengguna
- 🌐 Dukungan Bahasa Indonesia & Inggris

## Persyaratan

- PHP ^8.2
- Composer
- Node.js & npm
- MySQL (atau SQLite untuk testing)

## Instalasi

```bash
# 1. Clone & masuk direktori
git clone <url-repo> perpusku
cd perpusku

# 2. Install dependency PHP
composer install

# 3. Copy environment & generate key
cp .env.example .env
php artisan key:generate

# 4. Setup database
#    Edit file .env, sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD
#    Lalu jalankan:
php artisan migrate --seed

# 5. Install & build asset frontend
npm install
npm run build

# 6. Jalankan server
php artisan serve
```

Buka `http://localhost:8000` di browser.

## Login

Setelah `--seed`, akun default:

| Email | Password |
|-------|----------|
| `admin@example.com` | `password` |

Atau daftar akun baru lewat halaman `/register`.

## Testing

```bash
php artisan test
```

Total **119 test**, semua pass. Menggunakan SQLite in-memory.

## Bahasa

Secara default aplikasi berbahasa **Indonesia** (`APP_LOCALE=id` di `.env`). Ganti ke Inggris dengan `APP_LOCALE=en`.

## Struktur Route

| Halaman | URL | Keterangan |
|---------|-----|------------|
| Login | `/login` | |
| Register | `/register` | |
| Dashboard | `/dashboard` | Riwayat peminjaman |
| Cari Buku | `/search-books` | |
| Keranjang | `/manage-cart` | |
| Konfirmasi Pinjam | `/confirm-borrow` | |
| Profil | `/profile` | |

## API

Project ini juga menyediakan REST API di `/api/*` — cocok untuk integrasi dengan frontend lain. Detail endpoint ada di [`PROJECT_SUMMARY.md`](./PROJECT_SUMMARY.md).
