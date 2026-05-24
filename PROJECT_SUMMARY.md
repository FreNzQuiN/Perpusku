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

### Frontend (JavaScript) — Blade + Bootstrap 5 CDN + vanilla JS
| Component | Version |
|-----------|---------|
| Build Tool | Vite ^6.0 (dev-only) |
| Tailwind CSS | ^4.0 (via Vite, unused in Blade) |
| React/MUI/Zustand/etc | devDependencies only (not used in UI) |
| Bootstrap 5 | CDN (actual UI framework) |

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
│   └── 2026_05_24_000002_add_soft_deletes_to_borrowings_tables.php
├── factories/
│   ├── BookFactory.php
│   └── UserFactory.php
└── seeders/
    ├── BookSeeder.php
    └── DatabaseSeeder.php

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

### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | auto-increment |
| name | string | |
| email | string | unique, stored lowercase |
| password | string | bcrypt |
| remember_token | string? | nullable |
| email_verified_at | timestamp? | nullable |

### `books`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| title | string | indexed (non-unique) |
| author | string | |
| stock | integer | default 0 |

### `borrowings`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | FK→users | cascade delete |
| borrow_date | date | |
| duration_days | integer | min:1, max:3 |
| deleted_at | timestamp? | soft deletes |
| created_at | timestamp | |
| updated_at | timestamp | |

### `borrowing_details`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| borrowing_id | FK→borrowings | cascade delete |
| book_id | FK→books | cascade delete |
| deleted_at | timestamp? | soft deletes |
| created_at | timestamp | |
| updated_at | timestamp | |

### `carts`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | FK→users | cascade delete |
| book_id | FK→books | cascade delete |
| UNIQUE(user_id, book_id) | | prevents duplicates |

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
| POST | `/login` | Guest | Web login (returns 204 or redirect) |
| POST | `/register` | Guest | Web register (returns 204 or redirect) |
| POST | `/logout` | Auth | Web logout (returns 204) |
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
- `RegisterRequest` — lowercases email via `validationData()` for case-insensitive uniqueness

### API Resources
- `BookResource` — id, title, author, stock
- `CartResource` — id, book_id, book (loaded relation), created_at
- `BorrowingResource` — id, user_id, borrow_date, duration_days, details
- `BorrowingDetailResource` — id, book_id, book (loaded relation)

---

## Frontend Conventions

### `apiFetch()` (in `app.blade.php`)
- Resolves `/api/...` to full API URL
- Attaches CSRF token and Bearer token
- Auto-redirects on 401

### Notification System
- `showNotification(message, type)` where type: `success`|`error`|`info`
- Slides in from top-right, auto-dismisses after 3s
- Defined in `app.blade.php`

### Inline JavaScript
- All page-specific JS in `@section('scripts')` in each Blade template
- No module bundling for JS (uses vanilla `fetch()`)

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
| EdgeCaseCartTest | 21 | Cart edge cases |
| EdgeCaseBorrowingTest | 23 | Borrowing edge cases |
| EdgeCaseLoginTest | 17 | Login edge cases |
| EdgeCaseRegistrationTest | 16 | Registration edge cases |
| Auth/*Test | 8 | Breeze auth tests |
| AdvancedLibraryTest | 5 | Integration flows |
| ExampleTest | 1 | Sanity check |

**Total: 119 tests, 263 assertions**

---

## Improvements Implemented (this session)

### Critical
- **LIKE Injection fix**: `%` and `_` escaped with backslash in `BookController`
- **TOCTOU race condition fix**: Stock validation inside `DB::transaction()` with `lockForUpdate`
- **CSS gradient fix**: `background-color` → `background` in `dashboard.blade.php`

### High
- **N+1 query fix**: Batch fetch books + batch insert borrowing details
- **Missing index**: Added `INDEX` on `books.title`
- **Pagination**: `BookController::index()` uses `->paginate(20)`
- **React/MUI unused deps**: Moved 12 packages from `dependencies` → `devDependencies`
- **Vite config**: Created `vite.config.js`

### Medium
- **Form Requests**: `StoreCartRequest`, `StoreBorrowingRequest`
- **Global validation handler**: `ValidationException` returns consistent 422 JSON
- **Service layer**: `BorrowingService` extracted from controller
- **API Resources**: BookResource, CartResource, BorrowingResource, BorrowingDetailResource

### Low
- **alert() → Toast**: Custom `showNotification()` system replacing `alert()`
- **Soft Deletes**: Added to `Borrowing` and `BorrowingDetail` models + migration
- **Cleanup**: Removed unused imports, CSS universal reset
- **Stock check duality**: Removed stock check from cart add (validated only at borrow)
- **Case-insensitive email**: LoginRequest and RegisterRequest normalize email to lowercase

---

## Pre-existing Fixes Applied This Session
- `RegisteredUserController::store()` return type widened to include `RedirectResponse`
- `AuthenticatedSessionController::store()` catches `ValidationException` to return 401 for API, `response()->noContent()` for web
- `AuthenticatedSessionController::destroy()` returns `response()->noContent()` for web
- `RegisterRequest::validationData()` lowercases email before unique validation
- `LoginRequest::prepareForValidation()` lowercases and trims email
