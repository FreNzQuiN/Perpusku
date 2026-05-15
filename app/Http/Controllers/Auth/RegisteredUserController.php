<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => strtolower($request->input('email')),
                'password' => Hash::make($request->input('password')),
            ]);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'users_email_unique') || str_contains($e->getMessage(), 'UNIQUE constraint failed: users.email')) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'email' => ['Email sudah digunakan'],
                        ],
                    ], 422);
                }

                return back()
                    ->withInput($request->only('name', 'email'))
                    ->withErrors(['email' => 'Email sudah digunakan']);
            }

            report($e);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi nanti.',
                ], 500);
            }

            return back()->withInput($request->only('name', 'email'))->withErrors([
                'email' => 'Terjadi kesalahan pada sistem. Silakan coba lagi nanti.',
            ]);
        }

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
