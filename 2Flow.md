# Flow Aplikasi Perpustakaan

## 1. Flow Dari Sudut Pandang User

---

## A. Registrasi Pengguna

### Flow

1. User membuka halaman registrasi.
2. User mengisi:
   - Nama
   - Email
   - Password

3. User menekan tombol **Daftar**.
4. Jika data valid:
   - Akun berhasil dibuat.
   - User diarahkan ke halaman login.

5. Jika data tidak valid:
   - Sistem menampilkan pesan error.

### Pengujian

- Registrasi berhasil.
- Email sudah digunakan.
- Password kosong.
- Format email tidak valid.

---

## B. Login Pengguna

### Flow

1. User membuka halaman login.
2. User memasukkan email dan password.
3. User menekan tombol **Login**.
4. Jika berhasil:
   - User masuk ke dashboard/home.

5. Jika gagal:
   - Sistem menampilkan pesan login gagal.

### Pengujian

- Login berhasil.
- Password salah.
- Email tidak ditemukan.
- Input kosong.

---

## C. Pencarian Buku

### Flow

1. User membuka halaman daftar buku.
2. User memasukkan kata kunci judul buku.
3. Sistem menampilkan daftar buku yang sesuai.
4. User memilih buku yang ingin dipinjam.

### Pengujian

- Buku ditemukan.
- Buku tidak ditemukan.
- Pencarian sebagian kata.
- Pencarian tanpa input.

---

## D. Menambahkan Buku ke Daftar Pinjaman

### Flow

1. User memilih buku.
2. User menekan tombol **Tambah ke Daftar Pinjaman**.
3. Buku masuk ke daftar pinjaman sementara.
4. User dapat menambahkan buku lain.

### Pengujian

- Tambah 1 buku.
- Tambah beberapa buku.
- Tambah buku yang sama.
- Tambah hingga batas maksimal.

---

## E. Menghapus Buku dari Daftar Pinjaman

### Flow

1. User membuka daftar pinjaman sementara.
2. User memilih buku yang ingin dibatalkan.
3. User menekan tombol **Hapus**.
4. Buku dihapus dari daftar.

### Pengujian

- Hapus buku berhasil.
- Hapus seluruh daftar.
- Hapus buku yang tidak ada.

---

## F. Konfirmasi Peminjaman

### Flow

1. User menentukan lama peminjaman.
2. User menekan tombol **Konfirmasi Peminjaman**.
3. Jika valid:
   - Transaksi disimpan.
   - User menerima notifikasi berhasil.

4. Jika tidak valid:
   - Sistem menampilkan alasan penolakan.

### Pengujian

- Peminjaman berhasil.
- Durasi lebih dari 3 hari.
- Jumlah buku lebih dari 10.
- Daftar pinjaman kosong.

---

# 2. Flow Dari Sudut Pandang Sistem

---

## A. Proses Registrasi

### Flow Sistem

1. Sistem menerima request registrasi.
2. Sistem memvalidasi:
   - Semua field wajib diisi.
   - Format email valid.
   - Email belum digunakan.

3. Sistem melakukan hashing password.
4. Sistem menyimpan data user ke database.
5. Sistem mengirim response sukses.

### Validasi Sistem

- Email unik.
- Password minimal sesuai aturan.
- Data wajib lengkap.

---

## B. Proses Login

### Flow Sistem

1. Sistem menerima email dan password.
2. Sistem mencari user berdasarkan email.
3. Sistem mencocokkan password hash.
4. Jika valid:
   - Sistem membuat token/session login.

5. Jika gagal:
   - Sistem mengirim response unauthorized.

### Validasi Sistem

- User terdaftar.
- Password sesuai.

---

## C. Proses Pencarian Buku

### Flow Sistem

1. Sistem menerima keyword pencarian.
2. Sistem melakukan query berdasarkan judul buku.
3. Sistem mengembalikan daftar buku yang cocok.

### Validasi Sistem

- Keyword boleh sebagian.
- Jika kosong → tampilkan semua buku (opsional).

---

## D. Proses Penambahan Buku ke Daftar Pinjaman

### Flow Sistem

1. Sistem menerima ID buku.
2. Sistem memeriksa:
   - Buku tersedia.
   - Buku valid.

3. Sistem menyimpan buku ke daftar sementara/session/cart.

### Validasi Sistem

- Buku tersedia.
- Buku tidak duplikat (opsional).

---

## E. Proses Penghapusan Buku dari Daftar

### Flow Sistem

1. Sistem menerima ID buku.
2. Sistem mencari buku dalam daftar pinjaman sementara.
3. Sistem menghapus buku dari daftar.

### Validasi Sistem

- Buku ada di daftar.

---

## F. Proses Konfirmasi Peminjaman

### Flow Sistem

1. Sistem menerima:
   - Daftar buku
   - Lama peminjaman

2. Sistem melakukan validasi:
   - User sudah login.
   - Jumlah buku ≤ 10.
   - Durasi ≤ 3 hari.
   - Daftar buku tidak kosong.

3. Jika valid:
   - Sistem membuat transaksi peminjaman.
   - Sistem menyimpan detail buku yang dipinjam.

4. Sistem mengirim response berhasil.
5. Jika gagal:
   - Sistem mengirim response error sesuai validasi.

### Validasi Utama

#### Validasi Durasi

durasi\ peminjaman \leq 3\ hari

#### Validasi Jumlah Buku

jumlah\ buku \leq 10

---

# 3. Alur Besar Sistem (High-Level Flow)

```text
Register → Login → Cari Buku → Pilih Buku →
Tambah ke Daftar Pinjaman →
(Opsional: Tambah/Hapus Buku) →
Input Lama Peminjaman →
Validasi Sistem →
Konfirmasi Peminjaman →
Transaksi Berhasil
```

---

# 4. Daftar Skenario Pengujian Utama

| Modul          | Skenario                   |
| -------------- | -------------------------- |
| Register       | Registrasi valid & invalid |
| Login          | Login berhasil & gagal     |
| Search         | Buku ditemukan/tidak       |
| Borrow Cart    | Tambah & hapus buku        |
| Validation     | Maksimal 10 buku           |
| Validation     | Maksimal 3 hari            |
| Borrowing      | Konfirmasi berhasil        |
| Error Handling | Database gagal terhubung   |
