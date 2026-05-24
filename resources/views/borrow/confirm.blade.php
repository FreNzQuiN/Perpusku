@extends('layouts.app')

@section('title', 'Konfirmasi Peminjaman')

@section('content')

    <div style="max-width: 560px; margin: 0 auto;">
        <div class="search-section">
            <div class="search-header">
                <h4>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    Konfirmasi Peminjaman
                </h4>
            </div>
            <div class="search-body">
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
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" id="borrow_date" class="form-control" value="{{ date('Y-m-d') }}">
                        <div id="error-date" class="error-message"></div>
                    </div>
                    <div class="form-actions">
                        <a href="{{ route('cart.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" id="submit-btn" class="btn btn-primary">Konfirmasi Pinjam</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
