<?php

use App\Http\Controllers\WebController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [WebController::class, 'login'])->name('login');
    Route::get('/register', [WebController::class, 'register'])->name('register');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    Route::get('/search-books', [WebController::class, 'search'])->name('books.index');
    Route::get('/manage-cart', [WebController::class, 'cart'])->name('cart.index');
    Route::get('/confirm-borrow', [WebController::class, 'confirm'])->name('borrow.confirm');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/auth.php';
