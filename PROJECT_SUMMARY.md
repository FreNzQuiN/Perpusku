# Perpusku — Project Summary

## Overview
Library management system (Sistem Perpustakaan). Monolithic Laravel 12 app with REST API backend + Blade frontend. Users register, login, search books, add to cart, borrow books, view history, manage profile.

---

## Tech Stack

### Backend (PHP)
| Component | Version |
|-----------|---------|
| Laravel | ^12.0 |
| PHP | ^8.2 |
| Sanctum | ^4.0 |
| MySQL (dev) / SQLite (testing) | — |

### Frontend (JavaScript) — Blade + Bootstrap 5 (npm + Vite) + vanilla JS
| Component | Version |
|-----------|---------|
| Build Tool | Vite ^6.0 (dev-only) |
| Bootstrap 5 | ^5.3.8 (npm, actual UI framework) |
| laravel-vite-plugin | ^1.2.0 |

### Dev/Testing
| Tool | Version |
|------|---------|
| PHPUnit | ^11.5 |
| Laravel Breeze | ^2.4 |
| Laravel Sail | ^1.41 |
| Laravel Pint | ^1.24 |

---

## Directory Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── AuthenticatedSessionController.php  — login/logout
│   │   │   ├── RegisteredUserController.php        — register
│   │   │   ├── NewPasswordController.php
│   │   │   ├── PasswordResetLinkController.php
│   │   │   ├── EmailVerificationNotificationController.php
│   │   │   └── VerifyEmailController.php
│   │   ├── BookController.php          — GET /api/books (search + pagination)
│   │   ├── BorrowingController.php     — POST /api/borrowings, GET /api/my-borrowings
│   │   ├── CartController.php          — CRUD /api/cart
│   │   ├── Controller.php              — base
│   │   ├── ProfileController.php       — Web: profile edit/update
│   │   └── WebController.php           — Web: page rendering
│   ├── Middleware/
│   │   └── EnsureEmailIsVerified.php
│   └── Requests/
│       ├── Auth/LoginRequest.php
│       ├── Auth/RegisterRequest.php
│       ├── StoreCartRequest.php
│       └── StoreBorrowingRequest.php
├── Models/
│   ├── Book.php
│   ├── Borrowing.php          (SoftDeletes)
│   ├── BorrowingDetail.php    (SoftDeletes)
│   ├── Cart.php
│   └── User.php               (HasApiTokens)
├── Providers/
│   └── AppServiceProvider.php
├── Services/
│   └── BorrowingService.php
└── Http/Resources/
    ├── BookResource.php
    ├── CartResource.php
    ├── BorrowingResource.php
    └── BorrowingDetailResource.php

bootstrap/app.php                — routing, middleware, exception handlers

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2026_05_13_092214_create_personal_access_tokens_table.php
│   ├── 2026_05_13_092326_create_books_table.php
│   ├── 2026_05_13_092326_create_borrowings_table.php
│   ├── 2026_05_13_092327_create_borrowing_details_table.php
│   ├── 2026_05_13_093327_create_carts_table.php
│   ├── 2026_05_24_000001_add_index_to_books_title.php
│   ├── 2026_05_24_000002_add_soft_deletes_to_borrowings_tables.php
│   ├── 2026_05_24_000003_replace_title_index_with_fulltext.php
│   └── 2026_05_24_000004_add_unique_constraint_to_borrowing_details.php
├── factories/
│   ├── BookFactory.php
│   └── UserFactory.php
└── seeders/
    ├── BookSeeder.php
    └── DatabaseSeeder.php

lang/
├── en/borrowing.php             — English translations
└── id/borrowing.php             — Indonesian translations

resources/views/
├── layouts/app.blade.php        — main layout (topbar, sidebar, content, toast system)
├── auth/login.blade.php
├── auth/register.blade.php
├── dashboard.blade.php
├── books/index.blade.php
├── borrow/cart.blade.php
├── borrow/confirm.blade.php
└── profile/edit.blade.php

routes/
├── web.php                      — Blade page serving
├── api.php                      — REST API endpoints
├── auth.php                     — POST auth routes (login, register, logout, password)
└── console.php

tests/
├── Feature/
│   ├── Auth/AuthenticationTest.php
│   ├── Auth/RegistrationTest.php
│   ├── Auth/EmailVerificationTest.php
│   ├── Auth/PasswordResetTest.php
│   ├── AdvancedLibraryTest.php
│   ├── EdgeCaseBorrowingTest.php
│   ├── EdgeCaseCartTest.php
│   ├── EdgeCaseLoginTest.php
│   ├── EdgeCaseRegistrationTest.php
│   ├── EdgeCaseSearchTest.php
│   ├── ExampleTest.php
│   └── LibraryApiTest.php
└── TestCase.php
```

---

## Database Schema (5 tables)

CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE books (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    stock INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FULLTEXT fulltext_title (title)
);

CREATE TABLE borrowings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    borrow_date DATE NOT NULL,
    duration_days INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE borrowing_details (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    borrowing_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (borrowing_id) REFERENCES borrowings (id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE,
    UNIQUE KEY unique_borrowing_book (borrowing_id, book_id)
);

CREATE TABLE carts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_user_book (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE
);

---

## API Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/register` | Guest | Register. Returns `{success, token, user}` (201) |
| POST | `/api/login` | Guest | Login. Returns `{success, token, user}` (200) or 401 on failure |
| POST | `/api/logout` | Sanctum | Logout (200) |
| GET | `/api/user` | Sanctum | Current user |
| GET | `/api/books?title=` | Sanctum | Search books. Paginated (20/page). LIKE search with escaped wildcards. Returns `{data: [...], links, meta}` |
| GET | `/api/cart` | Sanctum | List cart items with book details |
| POST | `/api/cart` | Sanctum | Add book to cart (no stock check—validated at borrow time) |
| DELETE | `/api/cart/{id}` | Sanctum | Remove from cart |
| POST | `/api/borrowings` | Sanctum | Create borrowing. Stock check INSIDE transaction with `lockForUpdate`. Max 10 books, max 3 days |
| GET | `/api/my-borrowings` | Sanctum | Borrowing history with details |

---

## Web (Blade) Routes

| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| GET | `/` | — | Redirects to dashboard or login |
| GET | `/login` | Guest | Login page |
| GET | `/register` | Guest | Register page |
| POST | `/login` | Guest | Web login (redirect to dashboard) |
| POST | `/register` | Guest | Web register (redirect to dashboard) |
| POST | `/logout` | Auth | Web logout (redirect to login) |
| GET | `/dashboard` | Auth | Dashboard with borrowing history |
| GET | `/search-books` | Auth | Book search page |
| GET | `/manage-cart` | Auth | Cart management |
| GET | `/confirm-borrow` | Auth | Confirm borrowing |
| GET | `/profile` | Auth | Edit profile |
| PUT | `/profile` | Auth | Update profile |

---

## Key Architecture Decisions

### Auth Strategy
- **Dual auth**: Laravel session guards for Blade pages + Sanctum tokens for API
- Login creates BOTH a session AND a token
- Frontend JavaScript stores token in `localStorage`, attaches via `Authorization: Bearer`
- CSRF token in meta tag for API calls from Blade
- On logout: token revoked from DB, session destroyed, localStorage cleared

### Request Flow
1. User authenticates → gets `token` in response
2. Token stored in `localStorage` 
3. `apiFetch()` wrapper adds `Authorization: Bearer <token>` to all requests
4. 401 responses clear token and redirect to login

### Global Exception Handling (`bootstrap/app.php`)
| Exception | API Response |
|-----------|-------------|
| `AuthenticationException` | 401 `{success: false, message: "Unauthenticated"}` |
| `ValidationException` | 422 `{success: false, message: "Validation failed", errors: {...}}` |
| `NotFoundHttpException` | 404 `{success: false, message: "Resource not found"}` |
| Any `Throwable` | 500 `{success: false, message: "...", error: "..."}` |

### Borrowing Transaction
1. `DB::transaction()` wraps everything
2. `Book::whereIn(...)->lockForUpdate()` locks ALL requested books at once
3. Stock validation inside transaction (TOCTOU-safe)
4. Batch decrement stock
5. `DB::table('borrowing_details')->insert([...])` for batch insert
6. Cart items cleared after success
7. Throws `RuntimeException` if stock insufficient

### Error Response Consistency
All API responses follow `{success: bool, message: string, data?: ..., errors?: ...}`

### Form Requests
- `StoreCartRequest` — validates `book_id` exists
- `StoreBorrowingRequest` — validates borrow_date, duration_days, book_ids
- Custom `failedValidation()` returns 422 JSON for API routes
- `LoginRequest` — lowercases email via `prepareForValidation()`
- `RegisterRequest` — lowercases email via `prepareForValidation()`

### API Resources
- `BookResource` — id, title, author, stock, in_cart, cart_id, in_borrowing, borrowing (borrow_date, duration_days, return_date)
- `CartResource` — id, book_id, book (loaded relation), created_at
- `BorrowingResource` — id, user_id, borrow_date, duration_days, details
- `BorrowingDetailResource` — id, book_id, book (loaded relation)

---

## Frontend Conventions

### Script Organization (`resources/js/app.js`)
- All JS bundled via Vite (ES module)
- Shared utilities: `apiFetch()`, `showNotification()`, `showConfirmDialog()`, `esc()`, `BOOK_SVG`
- Layout behavior: sidebar toggle, profile dropdown, nav highlighting
- Page-specific logic guarded by `document.getElementById()` existence checks
- Functions called from `onclick` attributes exposed on `window`

---

## Testing

### Commands
```bash
php artisan test                     # all tests
php artisan test --filter=SearchTest # specific file
php artisan test --filter='test_search_case_insensitive'  # specific test
```

### Test database
- SQLite in-memory (configured in `phpunit.xml`)
- `RefreshDatabase` trait used in all feature tests

### Test categories
| File | Tests | Coverage |
|------|-------|----------|
| LibraryApiTest | 6 | Core CRUD flow |
| EdgeCaseSearchTest | 19 | Search edge cases |
| EdgeCaseCartTest | 22 | Cart edge cases |
| EdgeCaseBorrowingTest | 24 | Borrowing edge cases |
| EdgeCaseLoginTest | 17 | Login edge cases |
| EdgeCaseRegistrationTest | 16 | Registration edge cases |
| Auth/*Test | 8 | Breeze auth tests |
| AdvancedLibraryTest | 5 | Integration flows |
| ExampleTest | 1 | Sanity check |

**Total: 119 tests, 262 assertions**
