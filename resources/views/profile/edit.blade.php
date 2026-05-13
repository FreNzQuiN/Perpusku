@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profile Saya</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Ada kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Avatar -->
                    <div class="text-center mb-24" style="margin-bottom: 24px;">
                        <div
                            style="width: 80px; height: 80px; background: linear-gradient(135deg, #8b5cf6 0%, #06b6d4 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 32px; color: white; font-weight: 600;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <p class="mt-3 text-muted" style="color: #666666; margin-top: 12px; font-size: 13px;">Avatar standar
                        </p>
                    </div>

                    <!-- Profile Form -->
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nama -->
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly
                                style="background-color: #f9fafb; cursor: not-allowed;">
                            <small class="text-muted" style="color: #666666; font-size: 12px;">Email tidak dapat
                                diubah</small>
                        </div>

                        <!-- Bergabung Sejak -->
                        <div class="mb-3">
                            <label class="form-label">Bergabung Sejak</label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('d F Y') }}"
                                readonly style="background-color: #f9fafb; cursor: not-allowed;">
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2" style="gap: 12px; margin-top: 24px;">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary flex-grow-1">Kembali</a>
                            <button type="submit" class="btn btn-primary flex-grow-1">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection