<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class WebController extends Controller
{
    public function login(): View { return view('auth.login'); }
    public function register(): View { return view('auth.register'); }
    public function dashboard(): View { return view('dashboard'); }
    public function search(): View { return view('books.index'); }
    public function cart(): View { return view('borrow.cart'); }
    public function confirm(): View { return view('borrow.confirm'); }
}
