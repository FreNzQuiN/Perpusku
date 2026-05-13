# Rekomendasi Perbaikan UI/UX Perpusku

Berdasarkan audit bahasa dan alur aplikasi, berikut adalah beberapa area yang memerlukan penanganan khusus di sisi Frontend untuk meningkatkan kenyamanan pengguna.

## 1. Alert Global / Information Box
Saat ini pesan error hanya muncul di bawah field input. Namun, untuk error yang bersifat sistemik atau transaksi besar, diperlukan kotak informasi di bagian atas halaman.

### Masalah:
User mungkin tidak melihat error jika form sangat panjang atau jika error terjadi di luar input field (misal: database sedang maintenance).

### Solusi:
Tambahkan komponen Alert di `layouts/app.blade.php` atau di tiap halaman utama:
```html
<div id="global-alert" class="alert alert-danger d-none" role="alert">
  <!-- Pesan dinamis di sini -->
</div>
```
*Gunakan ini untuk pesan seperti: "Koneksi database terputus" atau "Maksimal 10 buku telah tercapai".*

## 2. Feedback Interaktif "Add to Cart"
Saat ini user hanya mendapatkan `alert()` browser ketika berhasil menambah buku.

### Masalah:
`alert()` browser mengganggu alur (*intrusive*) dan terlihat kurang profesional.

### Solusi:
Implementasikan **Toast Notification** atau **Snackbar** yang muncul di pojok layar selama 3 detik.
*Contoh: "✓ Berhasil ditambahkan ke daftar pinjaman"*

## 3. Visualisasi Stok yang Lebih Jelas
Saat ini stok hanya berupa teks angka.

### Masalah:
User sulit membedakan secara instan buku mana yang populer (stok menipis) dan mana yang habis.

### Solusi:
- Stok > 5: Badge Hijau.
- Stok 1-5: Badge Kuning + teks "Stok Terbatas!".
- Stok 0: Badge Merah + Button Disable.

## 4. Konfirmasi Tanggal Peminjaman
Saat ini user bisa memilih tanggal melalui input date standar.

### Masalah:
User sering lupa bahwa mereka harus mengembalikan dalam 3 hari.

### Solusi:
Di halaman konfirmasi, tampilkan **Estimasi Tanggal Kembali** secara otomatis berdasarkan input `duration_days` dan `borrow_date`.
*Contoh: "Anda memilih 2 hari, maka buku harus dikembalikan pada: 15 Mei 2026"*

## 5. Loading State Skeleton
Saat mencari buku, layar hanya menampilkan teks "Mencari...".

### Solusi:
Gunakan **Skeleton Screen** (kotak abu-abu yang berdenyut) agar aplikasi terasa lebih cepat dan modern.

---
*Laporan ini dibuat sebagai panduan untuk tim Frontend guna mencapai standar UI/UX yang telah ditetapkan di 4UIUX.md.*
