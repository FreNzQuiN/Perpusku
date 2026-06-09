<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    public function store(RegisterRequest $request): JsonResponse|Response|RedirectResponse
    {
        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
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

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Log::error('Gagal registrasi: ' . $e->getMessage());
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registrasi gagal. Silakan coba lagi.'
                ], 500);
            }
            return back()->with('error', 'Registrasi gagal. Silakan coba lagi.');
        }
    }
}
