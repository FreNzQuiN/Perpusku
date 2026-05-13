<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_user_can_search_books()
    {
        Book::create(['title' => 'Laravel for Beginners', 'author' => 'Author 1', 'stock' => 5]);
        Book::create(['title' => 'Advanced PHP', 'author' => 'Author 2', 'stock' => 3]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/books?title=Laravel');

        $response->assertStatus(200)
                 ->assertJsonCount(1)
                 ->assertJsonFragment(['title' => 'Laravel for Beginners']);
    }

    public function test_user_can_borrow_books()
    {
        $book = Book::create(['title' => 'Test Book', 'author' => 'Author', 'stock' => 5]);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrowings', [
            'borrow_date' => now()->toDateString(),
            'duration_days' => 2,
            'book_ids' => [$book->id],
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('borrowings', ['user_id' => $user->id, 'duration_days' => 2]);
        $this->assertEquals(4, $book->fresh()->stock);
    }

    public function test_user_cannot_borrow_more_than_3_days()
    {
        $book = Book::create(['title' => 'Test Book', 'author' => 'Author', 'stock' => 5]);
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

    public function test_user_cannot_borrow_more_than_10_books()
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
}
