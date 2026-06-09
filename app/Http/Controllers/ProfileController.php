<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            Auth::user()->update($request->validated());
            return back()->with('success', 'Profile berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Gagal update profile: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui profile. Silakan coba lagi.');
        }
    }
}
