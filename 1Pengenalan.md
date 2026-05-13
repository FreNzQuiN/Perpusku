# Plan Implementasi Aplikasi Monolithic Perpustakaan

## Tujuan

Membangun aplikasi perpustakaan berbasis **Laravel + REST API + MySQL** dengan fitur utama registrasi, login, pencarian buku, dan proses peminjaman buku sesuai prioritas **MoSCoW** yang telah ditentukan.

---

# Scope Implementasi

## Must Have (Akan Diimplementasikan)

### 1. Registrasi Pengguna

**User Story:** US-01

Fitur:

- Pengguna dapat membuat akun baru.
- Validasi email unik.
- Password disimpan secara aman (hashing).

Output:

- Endpoint registrasi API.
- Data pengguna tersimpan di database.

---

### 2. Login Pengguna

**User Story:** US-02

Fitur:

- Pengguna dapat login menggunakan email dan password.
- Sistem menghasilkan token autentikasi/session login.

Output:

- Endpoint login API.
- Proteksi endpoint peminjaman menggunakan autentikasi.

---

### 3. Pencarian Buku Berdasarkan Judul

**User Story:** US-03

Fitur:

- Pengguna dapat mencari buku berdasarkan kata kunci judul.
- Sistem menampilkan daftar buku yang sesuai.

Output:

- Endpoint pencarian buku.
- Response data buku dalam format JSON.

---

### 4. Proses Peminjaman Buku

**User Story:** US-04

Fitur:

- Pengguna memilih buku yang ingin dipinjam.
- Pengguna menentukan lama peminjaman.
- Sistem menyimpan transaksi peminjaman.

Output:

- Endpoint pembuatan transaksi peminjaman.
- Penyimpanan data transaksi dan detail buku.

---

### 5. Validasi Lama Peminjaman

**User Story:** US-06

Fitur:

- Sistem membatasi lama peminjaman maksimal 3 hari.

Validasi:

- Jika lebih dari 3 hari → transaksi ditolak.

---

### 6. Validasi Jumlah Buku

**User Story:** US-07

Fitur:

- Maksimal 10 buku dalam satu transaksi peminjaman.

Validasi:

- Jika lebih dari 10 buku → transaksi ditolak.

---

# Should Have (Akan Diimplementasikan Jika Waktu Memungkinkan)

### 7. Manajemen Daftar Pinjaman

**User Story:** US-05

Fitur:

- Menambahkan buku lain ke daftar pinjaman sebelum konfirmasi.
- Menghapus buku dari daftar sebelum konfirmasi.

Output:

- Keranjang/list sementara peminjaman.

---

# Could Have (Opsional)

### 8. Pesan Error Koneksi Database

Fitur:

- Sistem menampilkan pesan error ketika database gagal diakses.

Contoh:

- “Database connection failed.”

---

# Won’t Have (Tidak Diimplementasikan)

Fitur berikut tidak termasuk dalam scope:

- Perhitungan denda keterlambatan.
- Login administrator.
- Dashboard admin.
- Pengembalian buku.
- Manajemen stok lanjutan.

---

# Arsitektur Sistem

## Teknologi

- Backend: Laravel
- API: REST API
- Database: MySQL

---

# Struktur Modul Utama

## Modul Authentication

- Register
- Login
- Logout (opsional)

## Modul Buku

- List buku
- Search buku berdasarkan judul

## Modul Peminjaman

- Tambah buku ke daftar pinjaman
- Hapus buku dari daftar
- Konfirmasi peminjaman
- Validasi aturan peminjaman

---

# Desain Database Sederhana

## Tabel users

- id
- name
- email
- password

## Tabel books

- id
- title
- author
- stock

## Tabel borrowings

- id
- user_id
- borrow_date
- duration_days

## Tabel borrowing_details

- id
- borrowing_id
- book_id

---

# Validasi Sistem

## Validasi Durasi

Maksimal:
durasi\ peminjaman \leq 3\ hari

---

## Validasi Jumlah Buku

Maksimal:
jumlah\ buku \leq 10

---

# Deliverables

## Backend

- REST API Laravel
- Endpoint authentication
- Endpoint buku
- Endpoint peminjaman

## Database

- Schema MySQL
- Seeder data buku

## Dokumentasi

- Dokumentasi endpoint API
- Cara menjalankan project

---

# Estimasi Tahapan Pengerjaan

| Tahap | Deskripsi                         |
| ----- | --------------------------------- |
| 1     | Setup project Laravel & database  |
| 2     | Implementasi authentication       |
| 3     | Implementasi fitur pencarian buku |
| 4     | Implementasi transaksi peminjaman |
| 5     | Implementasi validasi sistem      |
| 6     | Testing API                       |
| 7     | Dokumentasi                       |

---

# Kriteria Verifikasi Client

Aplikasi dianggap sesuai apabila:

- User dapat register dan login.
- User dapat mencari buku berdasarkan judul.
- User dapat meminjam buku.
- Sistem menolak peminjaman > 3 hari.
- Sistem menolak peminjaman > 10 buku.
- User dapat mengelola daftar buku sebelum konfirmasi (should have).
