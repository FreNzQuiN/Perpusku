<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_manage_cart()
    {
        $user = User::factory()->create();
        $book = Book::create(['title' => 'Cart Book', 'author' => 'Author', 'stock' => 5]);
        $this->actingAs($user);

        // Add to cart
        $response = $this->postJson('/api/cart', ['book_id' => $book->id]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('carts', ['user_id' => $user->id, 'book_id' => $book->id]);

        // Get cart
        $response = $this->getJson('/api/cart');
        $response->assertStatus(200)->assertJsonCount(1, 'data');

        // Remove from cart
        $cartId = $response->json('data.0.id');
        $response = $this->deleteJson("/api/cart/{$cartId}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('carts', ['id' => $cartId]);
    }

    public function test_user_can_borrow_without_borrow_date_uses_today()
    {
        $user = User::factory()->create();
        $book = Book::create(['title' => 'Book', 'author' => 'Author', 'stock' => 5]);
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'duration_days' => 2,
            'book_ids' => [$book->id],
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('borrowings', [
            'user_id' => $user->id,
            'borrow_date' => now()->toDateString(),
        ]);
    }

    public function test_cannot_borrow_duplicate_books_in_one_transaction()
    {
        $user = User::factory()->create();
        $book = Book::create(['title' => 'Book', 'author' => 'Author', 'stock' => 5]);
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'duration_days' => 2,
            'book_ids' => [$book->id, $book->id],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_ids.0', 'book_ids.1']);
    }

    public function test_user_can_see_borrowing_history()
    {
        $user = User::factory()->create();
        $book = Book::create(['title' => 'History Book', 'author' => 'Author', 'stock' => 5]);
        $this->actingAs($user);

        $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [$book->id],
        ]);

        $response = $this->getJson('/api/my-borrowings');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'History Book']);
    }

    public function test_global_error_handler_for_404()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/non-existent-route');
        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Resource not found']);
    }
}
