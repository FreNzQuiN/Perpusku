<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\JsonResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // This handles validation and rate limiting via Breeze's LoginRequest
        $request->authenticate();

        // Regenerate session to prevent session fixation and establish new CSRF token
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $user = Auth::user();

        // For API/AJAX requests, return standardized JSON with token
        if ($request->wantsJson() || $request->is('api/*')) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => $user
            ], 200);
        }

        // Fallback for standard web form submit
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        // Revoke token if it exists
        if ($user && method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout'
            ], 200);
        }

        return redirect('/');
    }
}
