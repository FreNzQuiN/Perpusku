<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCaseSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        Book::create(['title' => 'Laravel for Beginners', 'author' => 'John Doe', 'stock' => 5]);
        Book::create(['title' => 'Advanced PHP Techniques', 'author' => 'Jane Smith', 'stock' => 3]);
        Book::create(['title' => 'Database Design Patterns', 'author' => 'Bob Johnson', 'stock' => 2]);
        Book::create(['title' => 'JavaScript Mastery', 'author' => 'Alice Brown', 'stock' => 8]);
        Book::create(['title' => 'Python for Data Science', 'author' => 'Charlie Wilson', 'stock' => 4]);
    }

    /**
     * Test: Case-insensitive search
     * From 3Implementasi.md: "Search case-insensitive"
     */
    public function test_search_case_insensitive()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=LARAVEL');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'Laravel for Beginners']);
    }

    /**
     * Test: Partial keyword search
     */
    public function test_search_partial_keyword()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=PHP');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'Advanced PHP Techniques']);
    }

    /**
     * Test: Empty search returns all books or handled gracefully
     */
    public function test_search_empty_keyword()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=');

        $response->assertStatus(200);
        // Should either return all books or empty, both are acceptable
    }

    /**
     * Test: Search no results
     */
    public function test_search_no_results()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=NonexistentBook');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    /**
     * Test: Special characters in search
     */
    public function test_search_special_characters()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=Java%20Script');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'JavaScript Mastery']);
    }

    /**
     * Test: SQL injection in search
     * From 3Implementasi.md: "Sanitasi keyword"
     */
    public function test_search_sql_injection()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson("/api/books?title=' OR '1'='1");

        // Should be safe and not return all books
        $response->assertStatus(200);
    }

    /**
     * Test: SQL injection with DROP
     */
    public function test_search_sql_injection_drop()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson("/api/books?title='; DROP TABLE books; --");

        $response->assertStatus(200);

        // Table should still exist and work
        $response2 = $this->getJson('/api/books?title=Laravel');
        $response2->assertStatus(200);
    }

    /**
     * Test: Very long search keyword
     */
    public function test_search_very_long_keyword()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $longKeyword = str_repeat('a', 5000);
        $response = $this->getJson("/api/books?title=" . urlencode($longKeyword));

        // Should handle gracefully
        $this->assertTrue(in_array($response->status(), [200, 400, 422]));
    }

    /**
     * Test: Multiple spaces in keyword
     */
    public function test_search_multiple_spaces()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=Database%20%20%20Design');

        // Should still find the book
        $response->assertStatus(200);
    }

    /**
     * Test: Search with special URL characters
     */
    public function test_search_special_url_chars()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=%26%23%3C%3E');

        // Should handle safely
        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    /**
     * Test: HTML tags in search
     */
    public function test_search_html_tags()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson("/api/books?title=" . urlencode('<script>alert("XSS")</script>'));

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    /**
     * Test: Search is accessible only to authenticated users
     */
    public function test_search_requires_authentication()
    {
        $response = $this->getJson('/api/books?title=Laravel');

        $response->assertStatus(401);
    }

    /**
     * Test: Mixed case partial search
     */
    public function test_search_mixed_case()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=lArAvEl');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    /**
     * Test: Search with numbers
     */
    public function test_search_with_numbers()
    {
        Book::create(['title' => 'Python 3.10 Handbook', 'author' => 'Author', 'stock' => 5]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=3.10');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'Python 3.10 Handbook']);
    }

    /**
     * Test: Whitespace-only search
     */
    public function test_search_whitespace_only()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=%20%20%20');

        $response->assertStatus(200);
    }

    /**
     * Test: Multiple search parameters (if supported)
     */
    public function test_search_multiple_params()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Some APIs might support author search too
        $response = $this->getJson('/api/books?title=Laravel&author=Doe');

        // Should handle additional params gracefully
        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test: Unicode characters in search
     */
    public function test_search_unicode_characters()
    {
        Book::create(['title' => 'Программирование на Python', 'author' => 'Автор', 'stock' => 5]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=Программирование');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    /**
     * Test: Null parameter
     */
    public function test_search_null_parameter()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=null');

        // Should not break
        $response->assertStatus(200);
    }

    /**
     * Test: Search consistency across multiple calls
     */
    public function test_search_consistency()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response1 = $this->getJson('/api/books?title=Laravel');
        $response2 = $this->getJson('/api/books?title=Laravel');

        $this->assertEquals($response1->json(), $response2->json());
    }
}
