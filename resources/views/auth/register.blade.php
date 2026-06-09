@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <div style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 60px);">
        <div style="width: 100%; max-width: 450px; padding: 20px;">
            <div class="card">
                <div class="card-header card-header-accent" style="text-align: center;">
                    <h4 style="margin-bottom: 0;">Daftar Akun Baru</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required>
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"
                            style="margin-top: 16px;">Daftar</button>
                    </form>
                    <div style="margin-top: 20px; text-align: center; font-size: 14px;">
                        Sudah punya akun? <a href="{{ route('login') }}"
                            style="color: #8b5cf6; text-decoration: none; font-weight: 500;">Login di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection