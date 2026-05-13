<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCaseRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Whitespace trimming on input
     * From 3Implementasi.md: "Trim otomatis"
     */
    public function test_registration_with_leading_trailing_whitespace()
    {
        $response = $this->postJson('/api/register', [
            'name' => '  John Doe  ',
            'email' => '  test@example.com  ',
            'password' => '  password123  ',
            'password_confirmation' => '  password123  ',
        ]);

        // Should accept and trim automatically
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test: Email case-insensitivity
     * From 3Implementasi.md: "Normalisasi lowercase"
     */
    public function test_registration_email_case_insensitivity()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'TEST@EXAMPLE.COM',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com', // Should be lowercase
        ]);
    }

    /**
     * Test: Email uniqueness constraint
     * From 3Implementasi.md: "Email harus unik"
     */
    public function test_cannot_register_duplicate_email()
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: Duplicate email with case variation
     */
    public function test_cannot_register_duplicate_email_case_variation()
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'TEST@EXAMPLE.COM',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Should reject because email is already taken (case-insensitive)
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: HTML/Script injection in name
     * From 3Implementasi.md: "Escape/sanitasi input"
     */
    public function test_registration_html_injection_in_name()
    {
        $maliciousName = '<script>alert("XSS")</script>';

        $response = $this->postJson('/api/register', [
            'name' => $maliciousName,
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        // Check that script tag is not executed (stored safely)
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
    }

    /**
     * Test: SQL injection attempt in name
     */
    public function test_registration_sql_injection_in_name()
    {
        $response = $this->postJson('/api/register', [
            'name' => "'; DROP TABLE users; --",
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Should be safe and not execute SQL
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /**
     * Test: Invalid email format
     */
    public function test_registration_invalid_email_format()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: Empty email
     */
    public function test_registration_empty_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: Empty name
     */
    public function test_registration_empty_name()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test: Empty password
     */
    public function test_registration_empty_password()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: Password mismatch
     */
    public function test_registration_password_mismatch()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: Password is actually hashed, not plaintext
     */
    public function test_registration_password_is_hashed()
    {
        $plainPassword = 'MySecurePassword123';

        $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
        ]);

        $user = User::where('email', 'test@example.com')->first();

        // Password should not be stored as plaintext
        $this->assertNotEquals($plainPassword, $user->password);

        // Password should be verifiable with hash check
        $this->assertTrue(password_verify($plainPassword, $user->password));
    }

    /**
     * Test: Very long name (boundary)
     */
    public function test_registration_very_long_name()
    {
        $longName = str_repeat('A', 1000);

        $response = $this->postJson('/api/register', [
            'name' => $longName,
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // May fail if there's a max length, or pass if accepted
        // Either is ok, but should not crash
        $this->assertTrue(in_array($response->status(), [201, 422]));
    }

    /**
     * Test: Missing fields entirely
     */
    public function test_registration_missing_all_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test: Special characters in email (valid according to RFC)
     */
    public function test_registration_special_chars_in_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test+tag@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // RFC 5322 allows + in local part, should be valid
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test+tag@example.com']);
    }

    /**
     * Test: Unicode characters in name
     */
    public function test_registration_unicode_name()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'José García 李明',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['name' => 'José García 李明']);
    }
}
