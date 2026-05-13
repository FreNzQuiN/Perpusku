# Strategi Implementasi dan Penanganan Test Case per Use Case

Dokumen ini menjelaskan pendekatan implementasi untuk setiap use case utama pada sistem perpustakaan, termasuk strategi penanganan **test case dasar**, **invalid input**, dan **edge case** agar sistem tetap stabil, aman, dan konsisten.

---

# 1. Use Case: Registrasi Pengguna

## Tujuan

Memastikan pengguna dapat membuat akun baru secara aman dan valid.

---

## Pendekatan Implementasi

### Validasi Input

Sistem akan memvalidasi:

- Nama tidak boleh kosong.
- Email wajib valid.
- Password wajib diisi.
- Email harus unik.

### Keamanan

- Password di-hash menggunakan bcrypt.
- Tidak menyimpan password plaintext.

### Response API

- Success response untuk registrasi berhasil.
- Error response terstruktur untuk validasi gagal.

---

## Penanganan Test Case Dasar

| Test Case           | Penanganan              |
| ------------------- | ----------------------- |
| Registrasi valid    | Simpan user ke database |
| Email sudah dipakai | Tolak registrasi        |
| Password kosong     | Tampilkan validasi      |
| Email tidak valid   | Tampilkan validasi      |
| Nama kosong         | Tampilkan validasi      |

---

## Penanganan Edge Case

| Edge Case                  | Strategi                         |
| -------------------------- | -------------------------------- |
| Spasi di awal/akhir input  | Trim otomatis                    |
| Email huruf besar/kecil    | Normalisasi lowercase            |
| Double click tombol daftar | Gunakan validasi unique database |
| Input script HTML          | Escape/sanitasi input            |
| Database gagal             | Return error generic             |

---

## Validasi Inti

### Email Harus Unik

email*{baru} \neq email*{tersimpan}

---

# 2. Use Case: Login Pengguna

## Tujuan

Memastikan hanya user valid yang dapat mengakses sistem.

---

## Pendekatan Implementasi

### Validasi Login

- Email wajib ada.
- Password wajib cocok dengan hash database.

### Session / Authentication

- Menggunakan token authentication Laravel Sanctum/JWT.
- Endpoint tertentu wajib login.

### Keamanan

- Password tidak pernah dikembalikan ke client.
- Token memiliki masa berlaku.

---

## Penanganan Test Case Dasar

| Test Case             | Penanganan              |
| --------------------- | ----------------------- |
| Login berhasil        | Generate token          |
| Password salah        | Return unauthorized     |
| Email tidak ditemukan | Return unauthorized     |
| Input kosong          | Return validation error |

---

## Penanganan Edge Case

| Edge Case          | Strategi                       |
| ------------------ | ------------------------------ |
| SQL Injection      | Gunakan ORM/Eloquent           |
| Brute force login  | Optional throttling/rate limit |
| Token expired      | Force login ulang              |
| Multi login device | Token dipisah per device       |
| Database lambat    | Timeout handling               |

---

## Validasi Inti

### Password Harus Cocok

hash(password*{input}) = hash(password*{database})

---

# 3. Use Case: Pencarian Buku

## Tujuan

Memungkinkan user menemukan buku dengan cepat berdasarkan judul.

---

## Pendekatan Implementasi

### Metode Search

- Menggunakan query LIKE.
- Mendukung pencarian parsial.

### Optimasi

- Pagination jika data besar.
- Query limit untuk mencegah overload.

---

## Penanganan Test Case Dasar

| Test Case            | Penanganan            |
| -------------------- | --------------------- |
| Buku ditemukan       | Tampilkan hasil       |
| Buku tidak ditemukan | Return list kosong    |
| Input sebagian kata  | Tetap tampilkan hasil |
| Input kosong         | Tampilkan semua buku  |

---

## Penanganan Edge Case

| Edge Case               | Strategi                     |
| ----------------------- | ---------------------------- |
| Huruf besar/kecil       | Search case-insensitive      |
| Karakter spesial        | Sanitasi keyword             |
| Keyword terlalu panjang | Batasi panjang input         |
| Query spam              | Optional debounce/rate limit |
| Data buku sangat banyak | Pagination                   |

---

## Validasi Inti

### Pencarian Parsial

judul\ buku\ LIKE\ %keyword%

---

# 4. Use Case: Peminjaman Buku

## Tujuan

Memastikan proses peminjaman sesuai aturan bisnis perpustakaan.

---

## Pendekatan Implementasi

### Flow Utama

1. User memilih buku.
2. Buku masuk ke daftar pinjaman sementara.
3. User menentukan durasi.
4. Sistem melakukan validasi.
5. Sistem menyimpan transaksi.

---

## Validasi Bisnis

### Maksimal Lama Peminjaman

durasi\ peminjaman \leq 3\ hari

### Maksimal Jumlah Buku

jumlah\ buku \leq 10

---

## Penanganan Test Case Dasar

| Test Case        | Penanganan       |
| ---------------- | ---------------- |
| Peminjaman valid | Simpan transaksi |
| Durasi > 3 hari  | Tolak transaksi  |
| Buku > 10        | Tolak transaksi  |
| Tidak ada buku   | Tolak transaksi  |
| User belum login | Unauthorized     |

---

## Penanganan Edge Case

| Edge Case               | Strategi                              |
| ----------------------- | ------------------------------------- |
| Double submit transaksi | Gunakan database transaction          |
| Buku dipilih berulang   | Cegah duplicate item                  |
| Buku sudah habis        | Validasi stok                         |
| Request timeout         | Atomic transaction rollback           |
| Concurrent borrowing    | Lock/update stok secara transactional |

---

## Konsistensi Database

### Pendekatan

Menggunakan:

- Database transaction.
- Rollback otomatis jika gagal.

### Tujuan

Mencegah:

- Data transaksi setengah tersimpan.
- Stok tidak sinkron.

---

# 5. Use Case: Manajemen Daftar Pinjaman

## Tujuan

Memberikan fleksibilitas sebelum konfirmasi peminjaman.

---

## Pendekatan Implementasi

### Fitur

- Tambah buku.
- Hapus buku.
- Simpan sementara sebelum konfirmasi.

### Penyimpanan

- Session sementara atau tabel cart sementara.

---

## Penanganan Test Case Dasar

| Test Case                | Penanganan        |
| ------------------------ | ----------------- |
| Tambah buku              | Simpan ke daftar  |
| Hapus buku               | Hapus dari daftar |
| Tambah banyak buku       | Validasi limit    |
| Konfirmasi daftar kosong | Tolak             |

---

## Penanganan Edge Case

| Edge Case                | Strategi                |
| ------------------------ | ----------------------- |
| Refresh browser          | Session tetap tersimpan |
| Buku dihapus saat proses | Re-validasi saat submit |
| Multi tab browser        | Sinkronisasi cart       |
| User logout mendadak     | Session dibersihkan     |

---

# Strategi Global Sistem

---

# 1. Validasi Backend Sebagai Sumber Utama

Walaupun frontend melakukan validasi:

- Semua validasi utama tetap dilakukan di backend.
- Backend tidak mempercayai data dari client.

---

# 2. Standard Response API

## Success Response

```json
{
  "success": true,
  "message": "Borrowing success",
  "data": {}
}
```

## Error Response

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {}
}
```

---

# 3. Penanganan Error Sistem

## Strategi

- Gunakan try-catch global.
- Logging error internal.
- Jangan tampilkan stack trace ke user.

---

# 4. Database Safety

## Pendekatan

- Foreign key constraint.
- Transaction database.
- Index untuk pencarian.

---

# 5. Pengujian Wajib

| Jenis Testing       | Fokus                  |
| ------------------- | ---------------------- |
| Unit Test           | Validasi bisnis        |
| Integration Test    | API & database         |
| Validation Test     | Input invalid          |
| Edge Case Test      | Concurrent & duplicate |
| Authentication Test | Token & session        |

---

# Kesimpulan Strategi

Sistem akan dibangun dengan prinsip:

- Validasi ketat di backend.
- Atomic transaction.
- Input sanitization.
- Authentication protection.
- Consistent API response.
- Penanganan edge case sejak awal implementasi.

Tujuannya agar sistem:

- Stabil saat digunakan.
- Aman dari input invalid.
- Tidak menghasilkan data corrupt.
- Tetap konsisten pada kondisi ekstrem maupun concurrent request.
