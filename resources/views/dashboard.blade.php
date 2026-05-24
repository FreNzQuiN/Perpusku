@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div>
        <div class="hero-card">
            <div class="card-header">
                <h3>Selamat Datang di Perpusku</h3>
                <p>Gunakan menu di samping untuk mencari buku atau kelola pinjaman Anda</p>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <h5 class="section-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/>
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17"/>
                </svg>
                Riwayat Peminjaman
            </h5>
            <div id="history-container">
                <div style="text-align: center; color: #9ca3af; padding: 40px 20px;">Memuat riwayat...</div>
            </div>
        </div>
    </div>
@endsection
