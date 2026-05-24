@extends('layouts.app')

@section('title', 'Cari Buku')

@section('content')

    <div>
        <div class="search-section">
            <div class="search-header">
                <h4>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Cari Buku
                </h4>
            </div>
            <div class="search-body">
                <div class="search-wrapper">
                    <div class="search-input-wrap">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" id="search-input" class="form-control" placeholder="Cari judul buku..." autofocus>
                    </div>
                    <button id="search-btn" class="btn-search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Cari
                    </button>
                </div>
            </div>
        </div>

        <div id="results-container" class="card-grid">
            <!-- Hasil buku di sini -->
        </div>
    </div>
@endsection