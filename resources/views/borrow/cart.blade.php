@extends('layouts.app')

@section('title', 'Daftar Pinjaman')

@section('content')

    <div>
        <div class="search-section">
            <div class="search-header" style="padding-bottom: 8px;">
                <h4>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                        <rect x="9" y="3" width="6" height="4" rx="1" />
                        <line x1="9" y1="12" x2="15" y2="12" />
                        <line x1="9" y1="16" x2="13" y2="16" />
                    </svg>
                    Keranjang Buku
                </h4>
            </div>
            <div class="search-body" style="padding-bottom: 0;">
                <div id="cart-container">
                    <div style="text-align: center; color: #9ca3af; padding: 40px 20px;">Memuat daftar...</div>
                </div>
            </div>
            <div id="action-container" class="cart-actions" style="display: none;">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Tambah Buku</a>
                <button type="button" class="btn btn-primary" onclick="openConfirmModal()">Konfirmasi Pinjam</button>
            </div>
        </div>
    </div>

    <div id="confirm-modal" class="confirm-modal-overlay">
        <div class="confirm-modal-box">
            <div class="confirm-modal-header">
                <h4>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    Konfirmasi Peminjaman
                </h4>
                <button type="button" class="confirm-modal-close" onclick="closeConfirmModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="confirm-modal-body">
                <div id="summary-container">
                    <div style="text-align: center; color: #9ca3af; padding: 20px;">Memuat...</div>
                </div>
                <form id="confirm-form">
                    <div class="mb-3">
                        <label class="form-label">Lama Peminjaman</label>
                        <input type="number" id="duration" class="form-control" min="1" max="3" value="1">
                        <div id="error-duration" class="error-message"></div>
                        <small style="color: #9ca3af; font-size: 12px;">Maksimal 3 hari</small>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeConfirmModal()">Batal</button>
                        <button type="submit" id="submit-btn" class="btn btn-primary">Konfirmasi Pinjam</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection