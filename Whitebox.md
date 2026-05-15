# Test Case TC01: Registrasi User

| Field          | Detail                                              |
| -------------- | --------------------------------------------------- |
| Fitur Uji      | Registrasi User                                     |
| Fungsi         | register()                                          |
| Lokasi         | app/Http/Controllers/AuthController.php             |
| Kode           | Validasi input registrasi dan penyimpanan user baru |
| Metode Testing | Whitebox Testing – Basic Path Testing               |

---

| Nama Jalur                                              | Prosedur Uji                                                       | Hasil yang Diharapkan                                                   | Hasil Pengujian              | Keterangan |
| ------------------------------------------------------- | ------------------------------------------------------------------ | ----------------------------------------------------------------------- | ---------------------------- | ---------- |
| Jalur 1 = Input valid → validasi sukses → user disimpan | Penguji mengisi nama, email valid, password valid lalu klik Daftar | Sistem berhasil membuat akun dan redirect ke login/dashboard            | Sistem berhasil membuat akun | Pass       |
| Jalur 2 = Email sudah digunakan → validasi gagal        | Penguji menggunakan email yang sudah terdaftar                     | Sistem menolak registrasi dan menampilkan pesan “Email sudah digunakan” | Sistem menolak registrasi    | Pass       |
| Jalur 3 = Email kosong → validasi gagal                 | Penguji mengosongkan email lalu klik Daftar                        | Sistem menampilkan error validasi email wajib diisi                     | Sistem menampilkan validasi  | Pass       |
| Jalur 4 = Password kosong → validasi gagal              | Penguji mengosongkan password                                      | Sistem menampilkan error password wajib diisi                           | Sistem menampilkan validasi  | Pass       |
| Jalur 5 = Format email salah → validasi gagal           | Penguji mengisi email tanpa format valid                           | Sistem menampilkan error format email invalid                           | Sistem menampilkan validasi  | Pass       |
| Jalur 6 = Database gagal → exception                    | Penguji memutus koneksi database saat registrasi                   | Sistem menampilkan pesan error sistem                                   | Sistem menampilkan error     | Pass       |

---

# Test Case TC02: Login User

| Field          | Detail                                  |
| -------------- | --------------------------------------- |
| Fitur Uji      | Login User                              |
| Fungsi         | login()                                 |
| Lokasi         | app/Http/Controllers/AuthController.php |
| Kode           | Validasi login dan autentikasi user     |
| Metode Testing | Whitebox Testing – Basic Path Testing   |

---

| Nama Jalur                               | Prosedur Uji                                             | Hasil yang Diharapkan                            | Hasil Pengujian             | Keterangan |
| ---------------------------------------- | -------------------------------------------------------- | ------------------------------------------------ | --------------------------- | ---------- |
| Jalur 1 = Credential valid → Auth sukses | Penguji mengisi email dan password benar lalu klik Login | Sistem berhasil login dan redirect ke dashboard  | Sistem berhasil login       | Pass       |
| Jalur 2 = Password salah → Auth gagal    | Penguji mengisi password salah                           | Sistem menampilkan pesan login gagal             | Sistem menampilkan error    | Pass       |
| Jalur 3 = Email tidak ditemukan          | Penguji menggunakan email yang tidak terdaftar           | Sistem menampilkan pesan login gagal             | Sistem menampilkan error    | Pass       |
| Jalur 4 = Email kosong                   | Penguji mengosongkan email                               | Sistem menampilkan validasi email wajib diisi    | Sistem menampilkan validasi | Pass       |
| Jalur 5 = Password kosong                | Penguji mengosongkan password                            | Sistem menampilkan validasi password wajib diisi | Sistem menampilkan validasi | Pass       |
| Jalur 6 = Database gagal saat login      | Penguji memutus koneksi database                         | Sistem menampilkan error sistem                  | Sistem menampilkan error    | Pass       |

---

# Test Case TC03: Pencarian Buku

| Field          | Detail                                  |
| -------------- | --------------------------------------- |
| Fitur Uji      | Pencarian Buku                          |
| Fungsi         | searchBook()                            |
| Lokasi         | app/Http/Controllers/BookController.php |
| Kode           | Query pencarian buku berdasarkan judul  |
| Metode Testing | Whitebox Testing – Basic Path Testing   |

---

| Nama Jalur                        | Prosedur Uji                         | Hasil yang Diharapkan                         | Hasil Pengujian                 | Keterangan |
| --------------------------------- | ------------------------------------ | --------------------------------------------- | ------------------------------- | ---------- |
| Jalur 1 = Keyword ditemukan       | Penguji mencari judul buku valid     | Sistem menampilkan daftar buku sesuai keyword | Sistem menampilkan buku         | Pass       |
| Jalur 2 = Keyword tidak ditemukan | Penguji mencari judul yang tidak ada | Sistem menampilkan data kosong                | Sistem menampilkan empty state  | Pass       |
| Jalur 3 = Keyword kosong          | Penguji search tanpa keyword         | Sistem menampilkan seluruh daftar buku        | Sistem menampilkan seluruh buku | Pass       |
| Jalur 4 = Input karakter spesial  | Penguji memasukkan simbol khusus     | Sistem tetap aman dan tidak crash             | Sistem aman                     | Pass       |
| Jalur 5 = Query database gagal    | Penguji memutus koneksi database     | Sistem menampilkan error sistem               | Sistem menampilkan error        | Pass       |

---

# Test Case TC04: Tambah Buku ke Daftar Pinjaman

| Field          | Detail                                        |
| -------------- | --------------------------------------------- |
| Fitur Uji      | Tambah Buku ke Cart                           |
| Fungsi         | addToCart()                                   |
| Lokasi         | app/Http/Controllers/CartController.php       |
| Kode           | Menambahkan buku ke daftar pinjaman sementara |
| Metode Testing | Whitebox Testing – Basic Path Testing         |

---

| Nama Jalur                       | Prosedur Uji                           | Hasil yang Diharapkan              | Hasil Pengujian          | Keterangan |
| -------------------------------- | -------------------------------------- | ---------------------------------- | ------------------------ | ---------- |
| Jalur 1 = Buku valid ditambahkan | Penguji memilih buku lalu klik Tambah  | Buku masuk ke daftar pinjaman      | Buku berhasil masuk      | Pass       |
| Jalur 2 = Buku duplicate         | Penguji menambahkan buku yang sama     | Sistem menolak duplicate           | Sistem menolak duplicate | Pass       |
| Jalur 3 = Buku tidak ditemukan   | Penguji mengirim ID buku invalid       | Sistem menampilkan error           | Sistem menampilkan error | Pass       |
| Jalur 4 = Jumlah buku > 10       | Penguji menambahkan lebih dari 10 buku | Sistem menolak penambahan          | Sistem menolak           | Pass       |
| Jalur 5 = User belum login       | Penguji mengakses cart tanpa login     | Sistem redirect/login unauthorized | Sistem menolak akses     | Pass       |

---

# Test Case TC05: Hapus Buku dari Daftar Pinjaman

| Field          | Detail                                        |
| -------------- | --------------------------------------------- |
| Fitur Uji      | Hapus Buku dari Cart                          |
| Fungsi         | removeFromCart()                              |
| Lokasi         | app/Http/Controllers/CartController.php       |
| Kode           | Menghapus buku dari daftar pinjaman sementara |
| Metode Testing | Whitebox Testing – Basic Path Testing         |

---

| Nama Jalur                         | Prosedur Uji                        | Hasil yang Diharapkan            | Hasil Pengujian              | Keterangan |
| ---------------------------------- | ----------------------------------- | -------------------------------- | ---------------------------- | ---------- |
| Jalur 1 = Buku berhasil dihapus    | Penguji klik tombol Hapus           | Buku hilang dari daftar pinjaman | Buku berhasil dihapus        | Pass       |
| Jalur 2 = Buku tidak ada di daftar | Penguji menghapus ID yang tidak ada | Sistem menampilkan error         | Sistem menampilkan error     | Pass       |
| Jalur 3 = Session cart kosong      | Penguji menghapus saat cart kosong  | Sistem menampilkan cart kosong   | Sistem menampilkan informasi | Pass       |

---

# Test Case TC06: Konfirmasi Peminjaman

| Field          | Detail                                        |
| -------------- | --------------------------------------------- |
| Fitur Uji      | Konfirmasi Peminjaman                         |
| Fungsi         | confirmBorrow()                               |
| Lokasi         | app/Http/Controllers/BorrowController.php     |
| Kode           | Validasi dan penyimpanan transaksi peminjaman |
| Metode Testing | Whitebox Testing – Basic Path Testing         |

---

| Nama Jalur                              | Prosedur Uji                                | Hasil yang Diharapkan                 | Hasil Pengujian           | Keterangan |
| --------------------------------------- | ------------------------------------------- | ------------------------------------- | ------------------------- | ---------- |
| Jalur 1 = Data valid → transaksi sukses | Penguji memilih ≤10 buku dan durasi ≤3 hari | Sistem menyimpan transaksi peminjaman | Sistem berhasil menyimpan | Pass       |
| Jalur 2 = Durasi > 3 hari               | Penguji memasukkan durasi 4 hari            | Sistem menolak transaksi              | Sistem menolak            | Pass       |
| Jalur 3 = Jumlah buku > 10              | Penguji meminjam 11 buku                    | Sistem menolak transaksi              | Sistem menolak            | Pass       |
| Jalur 4 = Cart kosong                   | Penguji konfirmasi tanpa buku               | Sistem menampilkan error              | Sistem menampilkan error  | Pass       |
| Jalur 5 = User belum login              | Penguji akses endpoint tanpa login          | Sistem unauthorized                   | Sistem menolak akses      | Pass       |
| Jalur 6 = Buku habis saat submit        | Penguji submit saat stok buku habis         | Sistem membatalkan transaksi          | Sistem menolak transaksi  | Pass       |
| Jalur 7 = Database gagal saat transaksi | Penguji memutus database saat submit        | Sistem rollback transaksi             | Sistem rollback berhasil  | Pass       |

---

# Test Case TC07: Logout

| Field          | Detail                                  |
| -------------- | --------------------------------------- |
| Fitur Uji      | Logout User                             |
| Fungsi         | logout()                                |
| Lokasi         | app/Http/Controllers/AuthController.php |
| Kode           | Menghapus session/token login           |
| Metode Testing | Whitebox Testing – Basic Path Testing   |

---

| Nama Jalur                      | Prosedur Uji                         | Hasil yang Diharapkan                    | Hasil Pengujian       | Keterangan |
| ------------------------------- | ------------------------------------ | ---------------------------------------- | --------------------- | ---------- |
| Jalur 1 = Logout berhasil       | Penguji klik Logout                  | Session/token dihapus dan redirect login | Logout berhasil       | Pass       |
| Jalur 2 = Session sudah expired | Penguji logout setelah session habis | Sistem tetap redirect login              | Sistem redirect login | Pass       |
