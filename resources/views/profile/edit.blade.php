@extends('layouts.app')

@section('title', 'Profile')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="search-section">
                <div class="search-header">
                    <h4>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        Profile Saya
                    </h4>
                </div>
                <div class="search-body">
                    @if ($errors->any())
                        <div class="alert alert-danger" style="margin-bottom: 20px;">
                            <strong>Ada kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success" style="margin-bottom: 20px;">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="text-center" style="margin-bottom: 24px;">
                        <div class="profile-avatar">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly
                                style="background-color: #f9fafb; cursor: not-allowed;">
                            <small class="text-muted" style="color: #9ca3af; font-size: 12px;">Email tidak dapat diubah</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bergabung Sejak</label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('d F Y') }}"
                                readonly style="background-color: #f9fafb; cursor: not-allowed;">
                        </div>

                        <div style="display: flex; gap: 12px; margin-top: 24px;">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary flex-grow-1"
                                style="border-radius: 10px; padding: 10px 16px; font-size: 14px; font-weight: 600;">Kembali</a>
                            <button type="submit" class="btn btn-primary flex-grow-1"
                                style="border-radius: 10px; padding: 10px 16px; font-size: 14px; font-weight: 600;">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
