<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => strtolower($request->input('email')),
            'password' => Hash::make($request->input('password')),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'token' => $token,
                'user' => $user
            ], 201);
        }

        return redirect()->intended(route('dashboard'));
    }
}
