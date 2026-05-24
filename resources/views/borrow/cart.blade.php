@extends('layouts.app')

@section('title', 'Daftar Pinjaman')

@section('content')

    <div>
        <div class="search-section">
            <div class="search-header" style="padding-bottom: 8px;">
                <h4>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <line x1="9" y1="12" x2="15" y2="12"/>
                        <line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                    Daftar Pinjaman Sementara
                </h4>
            </div>
            <div class="search-body" style="padding-bottom: 0;">
                <div id="cart-container">
                    <div style="text-align: center; color: #9ca3af; padding: 40px 20px;">Memuat daftar...</div>
                </div>
            </div>
            <div id="action-container" class="cart-actions" style="display: none;">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Tambah Buku</a>
                <a href="{{ route('borrow.confirm') }}" class="btn btn-primary">Konfirmasi Pinjam</a>
            </div>
        </div>
    </div>
@endsection
