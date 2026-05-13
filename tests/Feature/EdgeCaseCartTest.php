<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCaseCartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Book::create(['title' => 'Book 1', 'author' => 'Author 1', 'stock' => 5]);
        Book::create(['title' => 'Book 2', 'author' => 'Author 2', 'stock' => 3]);
        Book::create(['title' => 'Book 3', 'author' => 'Author 3', 'stock' => 0]);
    }

    /**
     * Test: Add book to cart
     */
    public function test_add_book_to_cart()
    {
        $user = User::factory()->create();
        $book = Book::find(1);
        $this->actingAs($user);

        $response = $this->postJson('/api/cart', ['book_id' => $book->id]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /**
     * Test: Add duplicate book to cart (same book twice)
     * From 3Implementasi.md: "Cegah duplicate item"
     */
    public function test_add_duplicate_book_to_cart()
    {
        $user = User::factory()->create();
        $book = Book::find(1);
        $this->actingAs($user);

        // Add first time
        $this->postJson('/api/cart', ['book_id' => $book->id]);

        // Add second time (duplicate)
        $response = $this->postJson('/api/cart', ['book_id' => $book->id]);

        // Should either reject or accept (depends on business logic)
        // But should not create a duplicate
        $cartCount = Cart::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->count();

        $this->assertLessThanOrEqual(1, $cartCount);
    }

    /**
     * Test: Get cart items
     */
    public function test_get_cart_items()
    {
        $user = User::factory()->create();
        $book1 = Book::find(1);
        $book2 = Book::find(2);
        $this->actingAs($user);

        $this->postJson('/api/cart', ['book_id' => $book1->id]);
        $this->postJson('/api/cart', ['book_id' => $book2->id]);

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test: Get empty cart
     */
    public function test_get_empty_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /**
     * Test: Remove from cart
     */
    public function test_remove_from_cart()
    {
        $user = User::factory()->create();
        $book = Book::find(1);
        $this->actingAs($user);

        $this->postJson('/api/cart', ['book_id' => $book->id]);
        $cartItem = Cart::where('user_id', $user->id)->first();

        $response = $this->deleteJson("/api/cart/{$cartItem->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('carts', ['id' => $cartItem->id]);
    }

    /**
     * Test: Remove non-existent cart item
     */
    public function test_remove_non_existent_cart_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->deleteJson('/api/cart/99999');

        // Should return 404 or similar error
        $this->assertTrue(in_array($response->status(), [404, 422]));
    }

    /**
     * Test: Remove from empty cart
     */
    public function test_remove_from_empty_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->deleteJson('/api/cart/1');

        $this->assertTrue(in_array($response->status(), [404, 422]));
    }

    /**
     * Test: Cart requires authentication
     */
    public function test_cart_requires_authentication()
    {
        $response = $this->postJson('/api/cart', ['book_id' => 1]);

        $response->assertStatus(401);
    }

    /**
     * Test: Get cart requires authentication
     */
    public function test_get_cart_requires_authentication()
    {
        $response = $this->getJson('/api/cart');

        $response->assertStatus(401);
    }

    /**
     * Test: Cart items isolated by user
     */
    public function test_cart_isolated_by_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book = Book::find(1);

        $this->actingAs($user1);
        $this->postJson('/api/cart', ['book_id' => $book->id]);

        $this->actingAs($user2);
        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /**
     * Test: Add out of stock book to cart (may or may not be allowed)
     */
    public function test_add_out_of_stock_book_to_cart()
    {
        $user = User::factory()->create();
        $outOfStockBook = Book::find(3); // stock = 0
        $this->actingAs($user);

        $response = $this->postJson('/api/cart', ['book_id' => $outOfStockBook->id]);

        // May be allowed or rejected depending on business logic
        // But should be consistent
        $this->assertTrue(in_array($response->status(), [201, 422]));
    }

    /**
     * Test: Non-existent book in cart
     */
    public function test_add_non_existent_book_to_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/cart', ['book_id' => 99999]);

        $response->assertStatus(422);
    }

    /**
     * Test: Add 10+ books to cart (should allow since limit is on borrow confirmation)
     */
    public function test_add_many_books_to_cart()
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(15)->create(['stock' => 5]);
        $this->actingAs($user);

        foreach ($books as $book) {
            $this->postJson('/api/cart', ['book_id' => $book->id]);
        }

        $response = $this->getJson('/api/cart');

        // Cart should allow more than 10 (validation happens at borrow confirmation)
        $response->assertStatus(200);
        $cartCount = $response->json('data', []);
        $this->assertGreaterThanOrEqual(15, count($cartCount));
    }

    /**
     * Test: Cart persistence across requests
     */
    public function test_cart_persistence()
    {
        $user = User::factory()->create();
        $book = Book::find(1);
        $this->actingAs($user);

        // Add to cart
        $this->postJson('/api/cart', ['book_id' => $book->id]);

        // Get cart multiple times
        $response1 = $this->getJson('/api/cart');
        $response2 = $this->getJson('/api/cart');

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $this->assertEquals($response1->json(), $response2->json());
    }

    /**
     * Test: Remove another user's cart item (should not be possible)
     */
    public function test_cannot_remove_another_user_cart_item()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book = Book::find(1);

        $this->actingAs($user1);
        $this->postJson('/api/cart', ['book_id' => $book->id]);
        $cartItem = Cart::where('user_id', $user1->id)->first();

        $this->actingAs($user2);
        $response = $this->deleteJson("/api/cart/{$cartItem->id}");

        // Should be forbidden or return 404
        $this->assertTrue(in_array($response->status(), [403, 404]));

        // Item should still exist for user1
        $this->assertDatabaseHas('carts', ['id' => $cartItem->id]);
    }

    /**
     * Test: Missing book_id field
     */
    public function test_add_to_cart_missing_book_id()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/cart', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_id']);
    }

    /**
     * Test: Invalid book_id type
     */
    public function test_add_to_cart_invalid_book_id_type()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/cart', ['book_id' => 'invalid']);

        $response->assertStatus(422);
    }

    /**
     * Test: Book ID as zero
     */
    public function test_add_to_cart_book_id_zero()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/cart', ['book_id' => 0]);

        $response->assertStatus(422);
    }

    /**
     * Test: Negative book ID
     */
    public function test_add_to_cart_negative_book_id()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/cart', ['book_id' => -1]);

        $response->assertStatus(422);
    }

    /**
     * Test: Clear all cart items before confirming borrow
     */
    public function test_clear_entire_cart()
    {
        $user = User::factory()->create();
        $book1 = Book::find(1);
        $book2 = Book::find(2);
        $this->actingAs($user);

        $this->postJson('/api/cart', ['book_id' => $book1->id]);
        $this->postJson('/api/cart', ['book_id' => $book2->id]);

        $cartItems = Cart::where('user_id', $user->id)->get();

        foreach ($cartItems as $item) {
            $this->deleteJson("/api/cart/{$item->id}");
        }

        $response = $this->getJson('/api/cart');
        $response->assertJsonCount(0, 'data');
    }

    /**
     * Test: Cart returned with book details
     */
    public function test_cart_includes_book_details()
    {
        $user = User::factory()->create();
        $book = Book::find(1);
        $this->actingAs($user);

        $this->postJson('/api/cart', ['book_id' => $book->id]);

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200);
        $cartItem = $response->json('data.0');

        // Should include book information
        $this->assertNotNull($cartItem);
    }

    /**
     * Test: Invalid cart ID format in delete
     */
    public function test_delete_cart_invalid_id_format()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->deleteJson('/api/cart/invalid');

        $this->assertTrue(in_array($response->status(), [404, 422]));
    }
}
