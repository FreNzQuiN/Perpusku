<?php

namespace App\Http\Controllers;

class WebController extends Controller
{
    public function login() { return view('auth.login'); }
    public function register() { return view('auth.register'); }
    public function dashboard() { return view('dashboard'); }
    public function search() { return view('books.index'); }
    public function cart() { return view('borrow.cart'); }
    public function confirm() { return view('borrow.confirm'); }
}
