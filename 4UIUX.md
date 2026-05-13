# Panduan Implementasi UI/UX dan Integrasi Frontend-Backend

Dokumen ini menjelaskan kebutuhan implementasi untuk tim desain/frontend agar sinkron dengan seluruh flow, validasi, dan logika backend sistem perpustakaan.

Tujuan utama:

- UI sesuai dengan alur bisnis.
- Validasi frontend sinkron dengan backend.
- Struktur Blade dan JavaScript mendukung REST API Laravel.
- Mengurangi mismatch antara frontend dan backend.

---

# Struktur Halaman Utama

| Halaman               | Fungsi                            |
| --------------------- | --------------------------------- |
| Register              | Registrasi user                   |
| Login                 | Login user                        |
| Dashboard/Home        | Halaman utama setelah login       |
| Search Buku           | Cari dan pilih buku               |
| Daftar Pinjaman       | Keranjang/list pinjaman sementara |
| Konfirmasi Peminjaman | Finalisasi transaksi              |

---

# 1. Halaman Registrasi

## Tujuan

User membuat akun baru.

---

## Komponen HTML

### Form Register

Field:

- Nama
- Email
- Password
- Konfirmasi Password

### Tombol

- Daftar
- Link ke Login

---

## Validasi Frontend (JavaScript)

Validasi sebelum submit:

- Semua field wajib diisi.
- Format email valid.
- Password minimal panjang tertentu.
- Password dan konfirmasi harus sama.

---

## Validasi Backend (PHP Laravel)

Backend tetap memvalidasi:

- Email unik.
- Input kosong.
- Format email.
- Hash password.

---

## Kebutuhan Blade

File:

```text
resources/views/auth/register.blade.php
```

Komponen:

- Form Blade
- Error validation section
- Success message section

---

## Kebutuhan CSS

### Fokus Styling

- Form center layout
- Responsive mobile
- Error message merah
- Success message hijau
- Disabled button saat loading

---

## Kebutuhan JavaScript

### Fungsi

- Real-time validation.
- Disable tombol submit saat request berlangsung.
- Menampilkan loading state.

---

# 2. Halaman Login

## Tujuan

User masuk ke sistem.

---

## Komponen HTML

### Form Login

Field:

- Email
- Password

### Tombol

- Login
- Link register

---

## Validasi Frontend

- Email wajib.
- Password wajib.

---

## Validasi Backend

- Email ditemukan.
- Password cocok.

---

## Kebutuhan Blade

File:

```text
resources/views/auth/login.blade.php
```

---

## Kebutuhan CSS

### Fokus Styling

- Tampilan sederhana.
- Fokus accessibility.
- Loading indicator.

---

## Kebutuhan JavaScript

### Fungsi

- Submit async/fetch/AJAX.
- Menampilkan pesan error login gagal.
- Redirect setelah login berhasil.

---

# 3. Halaman Dashboard / Home

## Tujuan

Menjadi pusat navigasi user setelah login.

---

## Komponen HTML

### Navbar

- Nama user
- Logout
- Menu pencarian buku
- Menu daftar pinjaman

### Section

- Informasi singkat sistem
- Shortcut pencarian buku

---

## Kebutuhan Blade

File:

```text
resources/views/dashboard.blade.php
```

---

## Kebutuhan CSS

### Fokus Styling

- Layout responsive.
- Sidebar/navbar sederhana.
- User-friendly navigation.

---

# 4. Halaman Pencarian Buku

## Tujuan

User mencari buku berdasarkan judul.

---

## Komponen HTML

### Search Area

- Input keyword
- Tombol search

### Hasil Buku

Card/table:

- Judul
- Penulis
- Stok
- Tombol tambah pinjaman

---

## Validasi Frontend

- Batasi panjang keyword.
- Hindari submit spam.

---

## Validasi Backend

### Query

Backend menggunakan:
judul\ buku\ LIKE\ %keyword%

---

## Kebutuhan Blade

File:

```text
resources/views/books/index.blade.php
```

---

## Kebutuhan CSS

### Fokus Styling

- Card/list buku responsive.
- Highlight hasil pencarian.
- Empty state jika buku tidak ditemukan.

---

## Kebutuhan JavaScript

### Fungsi

- AJAX search realtime (opsional).
- Debounce input search.
- Update hasil tanpa reload halaman.

---

# 5. Halaman Daftar Pinjaman

## Tujuan

Menampilkan buku yang dipilih sebelum konfirmasi.

---

## Komponen HTML

### Tabel/List Buku

Kolom:

- Judul buku
- Penulis
- Tombol hapus

### Informasi

- Total buku dipilih

### Tombol

- Tambah buku lagi
- Konfirmasi peminjaman

---

## Validasi Frontend

### Maksimal Buku

Frontend dapat memberi warning jika:
jumlah\ buku > 10

Tetapi backend tetap menjadi validator utama.

---

## Validasi Backend

Backend memastikan:

- Maksimal 10 buku.
- Buku valid.
- Tidak duplicate.

---

## Kebutuhan Blade

File:

```text
resources/views/borrow/cart.blade.php
```

---

## Kebutuhan CSS

### Fokus Styling

- Table responsive.
- Empty cart state.
- Button action jelas.

---

## Kebutuhan JavaScript

### Fungsi

- Hapus item tanpa reload.
- Update total buku realtime.
- Disable tombol konfirmasi jika kosong.

---

# 6. Halaman Konfirmasi Peminjaman

## Tujuan

Finalisasi transaksi peminjaman.

---

## Komponen HTML

### Form Konfirmasi

Field:

- Lama peminjaman (hari)

### Ringkasan

- Daftar buku
- Total buku

### Tombol

- Konfirmasi
- Kembali

---

## Validasi Frontend

### Maksimal Durasi

durasi\ peminjaman \leq 3\ hari

Jika lebih:

- Tampilkan warning.
- Disable submit.

---

## Validasi Backend

Backend tetap memvalidasi:

- Maksimal 3 hari.
- Maksimal 10 buku.
- User login.
- Cart tidak kosong.

---

## Kebutuhan Blade

File:

```text
resources/views/borrow/confirm.blade.php
```

---

## Kebutuhan CSS

### Fokus Styling

- Summary transaksi jelas.
- Warning validation terlihat.
- Layout mobile friendly.

---

## Kebutuhan JavaScript

### Fungsi

- Validasi realtime durasi.
- Loading submit.
- Prevent double submit.

---

# Kebutuhan Backend Laravel (PHP)

---

# Struktur Controller

| Controller       | Fungsi                 |
| ---------------- | ---------------------- |
| AuthController   | Register & login       |
| BookController   | Search buku            |
| BorrowController | Peminjaman             |
| CartController   | Kelola daftar pinjaman |

---

# Struktur Route

## Web Route

```php
/routes/web.php
```

Digunakan untuk:

- Blade rendering.

---

## API Route

```php
/routes/api.php
```

Digunakan untuk:

- AJAX/fetch request.

---

# Middleware

## Authentication

Route berikut wajib login:

- Dashboard
- Borrow
- Cart

Middleware:

```php
auth
```

---

# Validasi Laravel

Gunakan:

```php
$request->validate([...]);
```

Agar:

- Error response konsisten.
- Validasi backend terpusat.

---

# Kebutuhan Database

## Tabel Users

- id
- name
- email
- password

## Tabel Books

- id
- title
- author
- stock

## Tabel Borrowings

- id
- user_id
- duration_days

## Tabel Borrowing Details

- id
- borrowing_id
- book_id

---

# Sinkronisasi Frontend dan Backend

## Aturan Penting

### Frontend

Berfungsi untuk:

- UX
- Interaksi user
- Validasi awal

### Backend

Tetap menjadi:

- Validator utama
- Pengontrol bisnis logic
- Penjaga integritas data

---

# Standard Response API

## Success

```json
{
    "success": true,
    "message": "Success"
}
```

## Error

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {}
}
```

---

# Standar UX yang Wajib Dijaga

| Area                  | Implementasi        |
| --------------------- | ------------------- |
| Loading State         | Semua submit/button |
| Error Message         | Jelas dan spesifik  |
| Empty State           | Search & cart       |
| Responsive            | Mobile & desktop    |
| Accessibility         | Label & focus input |
| Prevent Double Submit | Semua form penting  |

---

# Checklist Sinkronisasi Final

| Area                         | Status Implementasi |
| ---------------------------- | ------------------- |
| Register validation sinkron  | Wajib               |
| Login authentication sinkron | Wajib               |
| Search realtime sinkron      | Wajib               |
| Borrow validation sinkron    | Wajib               |
| Max 3 hari sinkron           | Wajib               |
| Max 10 buku sinkron          | Wajib               |
| Error handling sinkron       | Wajib               |
| API response sinkron         | Wajib               |
