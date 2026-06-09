<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCaseBorrowingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test books
        Book::create(['title' => 'Book 1', 'author' => 'Author 1', 'stock' => 5]);
        Book::create(['title' => 'Book 2', 'author' => 'Author 2', 'stock' => 1]);
        Book::create(['title' => 'Book 3', 'author' => 'Author 3', 'stock' => 0]); // Out of stock
    }

    /**
     * Test: Maximum duration validation (exactly 3 days)
     * From 3Implementasi.md: "durasi peminjaman ≤ 3 hari"
     */
    public function test_borrow_exactly_3_days()
    {
        $book = Book::find(1);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 3,
            'book_ids' => [$book->id],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('borrowings', ['duration_days' => 3]);
    }

    /**
     * Test: Exceed maximum duration (4 days)
     */
    public function test_borrow_exceeds_3_days()
    {
        $book = Book::find(1);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 4,
            'book_ids' => [$book->id],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_days']);
    }

    /**
     * Test: Zero duration
     */
    public function test_borrow_zero_duration()
    {
        $book = Book::find(1);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 0,
            'book_ids' => [$book->id],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_days']);
    }

    /**
     * Test: Negative duration
     */
    public function test_borrow_negative_duration()
    {
        $book = Book::find(1);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => -1,
            'book_ids' => [$book->id],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_days']);
    }

    /**
     * Test: Borrowing uses today's date even if client sends a different one
     */
    public function test_borrow_uses_today_date()
    {
        $book = Book::find(1);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'duration_days' => 2,
            'book_ids' => [$book->id],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('borrowings', [
            'user_id' => $user->id,
            'borrow_date' => now()->toDateString(),
        ]);
    }

    /**
     * Test: Maximum book count (exactly 10)
     * From 3Implementasi.md: "jumlah buku ≤ 10"
     */
    public function test_borrow_exactly_10_books()
    {
        $books = Book::factory()->count(10)->create(['stock' => 5]);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => $books->pluck('id')->toArray(),
        ]);

        $response->assertStatus(201);
    }

    /**
     * Test: Exceed maximum book count (11 books)
     */
    public function test_borrow_exceeds_10_books()
    {
        $books = Book::factory()->count(11)->create(['stock' => 5]);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => $books->pluck('id')->toArray(),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_ids']);
    }

    /**
     * Test: Duplicate books in one transaction
     * From AdvancedLibraryTest.php
     */
    public function test_borrow_duplicate_books()
    {
        $book = Book::find(1);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [$book->id, $book->id],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_ids.0', 'book_ids.1']);
    }

    /**
     * Test: Empty book list
     */
    public function test_borrow_empty_book_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_ids']);
    }

    /**
     * Test: Out of stock book
     */
    public function test_borrow_out_of_stock_book()
    {
        $outOfStockBook = Book::find(3); // stock = 0
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [$outOfStockBook->id],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_ids']);
    }

    /**
     * Test: Non-existent book ID
     */
    public function test_borrow_non_existent_book()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [99999],
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test: Stock decreases after borrowing
     */
    public function test_borrow_stock_decreases()
    {
        $book = Book::find(1);
        $initialStock = $book->stock;

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [$book->id],
        ]);

        $book->refresh();
        $this->assertEquals($initialStock - 1, $book->stock);
    }

    /**
     * Test: Multiple books stock decreases correctly
     */
    public function test_borrow_multiple_books_stock_decreases()
    {
        $book1 = Book::find(1);
        $book2 = Book::find(2);
        $initialStock1 = $book1->stock;
        $initialStock2 = $book2->stock;

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [$book1->id, $book2->id],
        ]);

        $book1->refresh();
        $book2->refresh();

        $this->assertEquals($initialStock1 - 1, $book1->stock);
        $this->assertEquals($initialStock2 - 1, $book2->stock);
    }

    /**
     * Test: Borrow requires authentication
     */
    public function test_borrow_requires_authentication()
    {
        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [1],
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test: Missing duration_days field
     */
    public function test_borrow_missing_duration()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'book_ids' => [1],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_days']);
    }

    /**
     * Test: Missing book_ids field
     */
    public function test_borrow_missing_book_ids()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_ids']);
    }

    /**
     * Test: Borrow can omit borrow_date
     */
    public function test_borrow_can_omit_borrow_date()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'duration_days' => 2,
            'book_ids' => [1],
        ]);

        $response->assertStatus(201);
    }

    /**
     * Test: Invalid borrow_date input is ignored
     */
    public function test_borrow_ignores_invalid_borrow_date_input()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => 'invalid-date',
            'duration_days' => 2,
            'book_ids' => [1],
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    /**
     * Test: Duration as string instead of number
     */
    public function test_borrow_duration_as_string()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 'two',
            'book_ids' => [1],
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test: Book IDs as string instead of array
     */
    public function test_borrow_book_ids_as_string()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => '1,2,3',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test: Very large duration number
     */
    public function test_borrow_very_large_duration()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 999999,
            'book_ids' => [1],
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test: Borrowing history records correctly
     */
    public function test_borrow_creates_correct_record()
    {
        $book = Book::find(1);
        $user = User::factory()->create();
        $this->actingAs($user);

        $borrowDate = now()->toDateString();
        $duration = 2;

        $this->postJson('/api/borrowings', [
            'borrow_date' => $borrowDate,
            'duration_days' => $duration,
            'book_ids' => [$book->id],
        ]);

        $this->assertDatabaseHas('borrowings', [
            'user_id' => $user->id,
            'duration_days' => $duration,
        ]);

        $this->assertDatabaseHas('borrowing_details', [
            'book_id' => $book->id,
        ]);
    }

    /**
     * Test: Multiple transactions by same user
     */
    public function test_borrow_multiple_transactions()
    {
        $books = Book::factory()->count(2)->create(['stock' => 5]);
        $user = User::factory()->create();
        $this->actingAs($user);

        // First borrow
        $response1 = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [$books[0]->id],
        ]);
        $response1->assertStatus(201);

        // Second borrow
        $response2 = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 1,
            'book_ids' => [$books[1]->id],
        ]);
        $response2->assertStatus(201);

        // Should have 2 borrowing records
        $this->assertEquals(2, Borrowing::where('user_id', $user->id)->count());
    }

    /**
     * Test: Future borrow_date input is ignored
     */
    public function test_borrow_ignores_future_borrow_date_input()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->addDay()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [1],
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }
}
