<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCaseLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Email case insensitivity on login
     * From 3Implementasi.md: "Case-insensitive"
     */
    public function test_login_with_uppercase_email()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'TEST@EXAMPLE.COM',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test: Email with extra whitespace
     */
    public function test_login_email_with_whitespace()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => '  test@example.com  ',
            'password' => 'password',
        ]);

        // Should trim and login successfully
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test: Non-existent email
     */
    public function test_login_non_existent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    /**
     * Test: Wrong password
     */
    public function test_login_wrong_password()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    /**
     * Test: Empty email
     */
    public function test_login_empty_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: Empty password
     */
    public function test_login_empty_password()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: Missing both fields
     */
    public function test_login_missing_both_fields()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test: SQL injection attempt in email
     * From 3Implementasi.md: "Gunakan ORM/Eloquent"
     */
    public function test_login_sql_injection_email()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => "test@example.com' OR '1'='1",
            'password' => 'password',
        ]);

        // Should reject - validation catches invalid email format
        $this->assertTrue(in_array($response->status(), [401, 422]));
    }

    /**
     * Test: SQL injection with comment syntax
     */
    public function test_login_sql_injection_with_comment()
    {
        $response = $this->postJson('/api/login', [
            'email' => "admin@example.com' --",
            'password' => 'anything',
        ]);

        // Should reject - validation catches invalid email format
        $this->assertTrue(in_array($response->status(), [401, 422]));
    }

    /**
     * Test: HTML injection attempt (if password is echoed somewhere)
     */
    public function test_login_html_injection_password()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => '<script>alert("XSS")</script>',
        ]);

        // Should safely reject and not execute script
        $response->assertStatus(401);
    }

    /**
     * Test: Invalid email format
     */
    public function test_login_invalid_email_format()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: Token is generated on successful login
     */
    public function test_login_generates_token()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        // Should have a token in response
        $this->assertNotNull($response->json('data.token') ?? $response->json('token'));
    }

    /**
     * Test: Multiple failed logins (for future rate limiting)
     */
    public function test_multiple_failed_logins()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        // Try 10 failed attempts
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);

            $response->assertStatus(401);
        }

        // System should still work (or implement throttling)
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'correctpassword',
        ]);

        // Should either still allow login or reject with rate limit message
        $this->assertTrue(in_array($response->status(), [200, 429, 401]));
    }

    /**
     * Test: Case sensitivity of password (should be case-sensitive)
     */
    public function test_login_password_case_sensitive()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('Password'),
        ]);

        // Try with different case
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test: Very long password attempt (boundary)
     */
    public function test_login_very_long_password()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => str_repeat('a', 10000),
        ]);

        // Should handle gracefully
        $this->assertTrue(in_array($response->status(), [401, 422]));
    }

    /**
     * Test: Special characters in password
     */
    public function test_login_special_characters_password()
    {
        $specialPassword = 'P@$$w0rd!#%&*()_+-=[]{}|;:,.<>?';

        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt($specialPassword),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => $specialPassword,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test: Unicode in email
     */
    public function test_login_unicode_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'тест@example.com',
            'password' => 'password',
        ]);

        // Should either handle or reject gracefully
        $this->assertTrue(in_array($response->status(), [401, 422]));
    }
}
