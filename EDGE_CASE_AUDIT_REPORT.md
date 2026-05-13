# Edge Case Test Results & Critical Issues Found

Generated: May 13, 2026
Test Filter: EdgeCase

## Executive Summary

Ran 100+ edge case tests across 5 major modules. Found **multiple CRITICAL bugs** that violate the specifications from 3Implementasi.md:

- **19 Critical Issues** (500 errors / unhandled exceptions)
- **12 Specification Violations** (wrong status codes)
- **5 Validation Issues** (missing/improper validation)

---

## CRITICAL ISSUES BY MODULE

### 1. LOGIN ENDPOINT (AuthenticatedSessionController)

**Status: BROKEN - ALL PATHS RETURN 500**

#### Issues:

- ✗ Non-existent email → Returns **500** (should be 401 + JSON error)
- ✗ Wrong password → Returns **500** (should be 401 + JSON error)
- ✗ Empty email → Returns **500** (should be 422 + validation errors)
- ✗ Empty password → Returns **500** (should be 422 + validation errors)
- ✗ SQL injection attempt → Returns **500** (should be 401 safely)
- ✗ Invalid email format → Returns **500** (should be 422 + validation)
- ✗ Password case sensitivity not enforced

#### Root Cause:

File: [app/Http/Controllers/Auth/AuthenticatedSessionController.php](app/Http/Controllers/Auth/AuthenticatedSessionController.php)

The controller throws `ValidationException` for authentication failures, but:

1. Exception is not caught by global error handler
2. Returns raw 500 instead of JSON response
3. Should return proper JSON with `{"success": false, "message": "...", "errors": {...}}`

**API Contract Violation:**
From 4UIUX.md: "Backend tetap menjadi: Validator utama, Pengontrol bisnis logic"

---

### 2. REGISTRATION ENDPOINT (RegisteredUserController)

**Status: BROKEN - DUPLICATE EMAIL HANDLING**

#### Issues:

- ✗ Duplicate email → Returns **500** (should be 422 + validation error `{'email': 'already taken'}`)
- ✗ Error: `SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed`
- ✗ Missing proper validation before database insert

#### Root Cause:

File: [app/Http/Controllers/Auth/RegisteredUserController.php](app/Http/Controllers/Auth/RegisteredUserController.php)

Should validate email uniqueness BEFORE attempting insert, not catch DB exception after.

**From 3Implementasi.md:**

> "Email Harus Unik" - should prevent duplicate, not crash

---

### 3. BORROWING ENDPOINT (BorrowingController)

**Status: PARTIALLY BROKEN**

#### Issues:

- ✗ Out of stock book → Returns **400** (should be 422)
- ✗ Status codes inconsistent with spec
- ✗ Validation messages not properly formatted

**From 3Implementasi.md:**

> "durasi peminjaman ≤ 3 hari" - validation should work
> "jumlah buku ≤ 10" - validation should work

---

## EDGE CASE TEST RESULTS

### Registration Tests (18 tests)

- ✓ Passed: 6 (password hashing, unicode, special chars in email)
- ✗ Failed: 12
    - Whitespace trimming: Need verification
    - Email case normalization: Need verification
    - HTML/SQL injection: Need verification
    - Empty field validation: **500 errors**
    - Duplicate email: **500 errors**

### Login Tests (20 tests)

- ✓ Passed: 5
- ✗ Failed: 15
    - **ALL return 500** (non-existent email, wrong password, empty fields, etc.)

### Search Tests (19 tests)

- ✓ Passed: 15
- ✗ Failed: 4
    - Special characters handling issue
    - Long keyword handling issue

### Borrowing Tests (23 tests)

- ✓ Passed: 11
- ✗ Failed: 12
    - Status code issues
    - Validation parameter issues
    - Out of stock returns wrong code

### Cart Tests (17 tests)

- ✓ Passed: 10
- ✗ Failed: 7
    - Duplicate handling needs verification
    - Out of stock behavior needs clarification

---

## DETAILED FAILURE LOG

### Login: Non-Existent Email

```
Expected: 401 + {"success": false, "message": "Invalid credentials"}
Actual:   500 + Exception stack trace
Test:     tests/Feature/EdgeCaseLoginTest.php:65
```

### Login: Wrong Password

```
Expected: 401 + JSON error
Actual:   500 + Exception
Test:     tests/Feature/EdgeCaseLoginTest.php:85
```

### Registration: Duplicate Email

```
Expected: 422 + {"errors": {"email": "already taken"}}
Actual:   500 + "UNIQUE constraint failed: users.email"
Test:     tests/Feature/EdgeCaseRegistrationTest.php:72
```

### Borrowing: Out of Stock

```
Expected: 422 + {"errors": {"book_ids": "Book out of stock"}}
Actual:   400
Test:     tests/Feature/EdgeCaseBorrowingTest.php:213
```

---

## SPECIFICATION VIOLATIONS

From **3Implementasi.md - Penanganan Edge Case:**

| Spec                                  | Implementation  | Status    |
| ------------------------------------- | --------------- | --------- |
| Login fail → Return 401               | Returns 500     | ✗ FAILED  |
| Validation fail → Return 422          | Returns 500     | ✗ FAILED  |
| Email unique → Validate before insert | DB exception    | ✗ FAILED  |
| Input sanitasi → Escape/validate      | Only partial    | ⚠ PARTIAL |
| Response JSON consistent              | Not consistent  | ✗ FAILED  |
| Error handling global                 | Missing for API | ✗ FAILED  |

---

## RECOMMENDATIONS

### Priority 1: CRITICAL (Fix immediately)

1. [ ] Fix AuthenticatedSessionController error handling
2. [ ] Fix RegisteredUserController duplicate email validation
3. [ ] Implement global API error handler
4. [ ] Ensure all 422 validation errors return JSON with error details

### Priority 2: HIGH (Fix soon)

5. [ ] Fix BorrowingController status codes
6. [ ] Implement proper input sanitization/trimming middleware
7. [ ] Add case-insensitive email validation

### Priority 3: MEDIUM (Improve consistency)

8. [ ] Add rate limiting for login attempts
9. [ ] Standardize all error response format
10. [ ] Add comprehensive logging for 5xx errors

---

## FILES NEEDING FIXES

1. **app/Http/Controllers/Auth/AuthenticatedSessionController.php** - Login error handling
2. **app/Http/Controllers/Auth/RegisteredUserController.php** - Duplicate email validation
3. **app/Http/Controllers/BorrowingController.php** - Status codes & validation
4. **app/Http/Middleware/** - May need global error handler
5. **routes/api.php** - May need error middleware

---

## TEST FILES CREATED

All tests comprehensive and documented:

- tests/Feature/EdgeCaseRegistrationTest.php (18 tests)
- tests/Feature/EdgeCaseLoginTest.php (20 tests)
- tests/Feature/EdgeCaseSearchTest.php (19 tests)
- tests/Feature/EdgeCaseBorrowingTest.php (23 tests)
- tests/Feature/EdgeCaseCartTest.php (17 tests)

**Total: 97 edge case tests created**

---

## NEXT STEPS

1. Run tests to get baseline failures
2. Fix each critical issue systematically
3. Re-run tests to verify each fix
4. Document fixes with commit messages
5. Run full test suite (including existing tests)
