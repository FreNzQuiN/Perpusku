-- ============================================
-- PHYSICAL ERD - PERPUSKU LIBRARY SYSTEM
-- Current Implementation (As Built)
-- Date: May 13, 2026
-- ============================================
-- TABLE: users (Peminjam)
-- Desc: Tabel pengguna/peminjam dengan autentikasi
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_email (email)
);

-- TABLE: books (Buku)
-- Desc: Tabel master buku dengan informasi dasar
CREATE TABLE books (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    stock INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_title (title)
);

-- TABLE: borrowings (Peminjaman)
-- Desc: Tabel transaksi peminjaman
-- Constraints: duration_days harus divalidasi di aplikasi (max 3 hari)
CREATE TABLE borrowings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    borrow_date DATE NOT NULL,
    duration_days INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_borrow_date (borrow_date)
);

-- TABLE: borrowing_details (Detail Peminjaman)
-- Desc: Detail per buku dalam satu transaksi peminjaman
-- Note: TIDAK ada kolom jumlah (quantity)
CREATE TABLE borrowing_details (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    borrowing_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (borrowing_id) REFERENCES borrowings (id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE,
    INDEX idx_borrowing_id (borrowing_id),
    INDEX idx_book_id (book_id)
);

-- TABLE: carts (Keranjang)
-- Desc: Tabel shopping cart untuk peminjaman sementara
-- Constraints: user_id dan book_id harus unik (1 book per cart per user)
CREATE TABLE carts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_user_book (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_book_id (book_id)
);

-- ============================================
-- RELATIONSHIP DIAGRAM (Text Format)
-- ============================================
/*
users (Peminjam)
================
|
PRIMARY KEY: id  |  (1)
|
┌───────────────┼───────────────┐
|               |               |
| (1:N)         | (1:N)         | (1:N)
|               |               |
carts        borrowings (Peminjaman)
=====        ===================
PK: id
FK: user_id → users.id

|
| (1:N)
|
borrowing_details (Detail)
=======================
PK: id
FK: borrowing_id → borrowings.id
FK: book_id → books.id
|
|
+----------→ books (Buku)
============
PK: id

carts
====
FK: user_id → users.id
FK: book_id → books.id


RELATIONSHIPS:
===============
1. users (1) ──→ (N) borrowings
- One user can have multiple borrowings
- ON DELETE CASCADE

2. users (1) ──→ (N) carts
- One user can have multiple items in cart
- ON DELETE CASCADE
- Composite Unique: (user_id, book_id)

3. borrowings (1) ──→ (N) borrowing_details
- One borrowing transaction can have multiple books
- ON DELETE CASCADE

4. books (1) ──→ (N) borrowing_details
- One book can be borrowed in multiple transactions
- ON DELETE CASCADE

5. books (1) ──→ (N) carts
- One book can be in multiple user carts
- ON DELETE CASCADE

*/