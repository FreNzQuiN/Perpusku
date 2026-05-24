<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
        Auth::user()->update($request->validated());

        return back()->with('success', 'Profile berhasil diperbarui!');
    }
}
