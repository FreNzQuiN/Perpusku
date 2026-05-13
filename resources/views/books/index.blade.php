@extends('layouts.app')

@section('title', 'Cari Buku')

@section('content')
    <style>
        @media (max-width: 768px) {
            .search-row {
                flex-direction: column !important;
                gap: 12px !important;
            }

            .search-row>div {
                width: 100% !important;
                padding: 0 !important;
            }
        }

        .card-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card-item {
            flex: 0 1 calc(33.333% - 14px);
            min-width: 280px;
        }

        @media (max-width: 1199px) {
            .card-item {
                flex: 0 1 calc(50% - 10px);
            }
        }

        @media (max-width: 768px) {
            .card-item {
                flex: 0 1 100%;
            }
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 style="font-weight: 600; margin-bottom: 20px;">🔍 Cari Buku</h4>
                <div class="row search-row" style="gap: 12px; display: flex;">
                    <div class="col-md-8" style="padding-right: 0;">
                        <input type="text" id="search-input" class="form-control" placeholder="Masukkan judul buku..."
                            style="border-radius: 8px;">
                    </div>
                    <div class="col-md-4" style="padding-left: 0;">
                        <button id="search-btn" class="btn btn-primary w-100"
                            style="border-radius: 8px; height: 40px;">Cari</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="results-container" class="card-grid" style="margin-top: 24px;">
            <!-- Hasil buku di sini -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const searchBooks = async (title = '') => {
            const results = document.getElementById('results-container');
            results.innerHTML = '<div style="text-align: center; color: #666666; width: 100%; padding: 20px;">Mencari...</div>';

            const response = await apiFetch(`/api/books?title=${title}`);
            const books = await response.json();

            if (books.length > 0) {
                results.innerHTML = books.map(book => `
                        <div class="card-item">
                            <div class="card h-100" style="transition: all 0.2s;">
                                <div class="card-body">
                                    <h5 style="font-weight: 600; margin-bottom: 8px; color: #1a1a1a; min-height: 2.5em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${book.title}</h5>
                                    <p style="color: #666666; font-size: 14px; margin-bottom: 12px;">${book.author}</p>
                                    <p class="badge ${book.stock > 0 ? 'bg-success' : 'bg-danger'}" style="margin-bottom: 16px;">Stok: ${book.stock}</p>
                                    <button onclick="addToCart(${book.id})" class="btn btn-primary w-100" ${book.stock <= 0 ? 'disabled' : ''} style="${book.stock <= 0 ? 'opacity: 0.6;' : ''}">
                                        Tambah Pinjaman
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
            } else {
                results.innerHTML = '<div class="alert alert-warning" style="width: 100%;">Maaf, buku tidak ditemukan.</div>';
            }
        };

        const addToCart = async (bookId) => {
            const response = await apiFetch('/api/cart', {
                method: 'POST',
                body: JSON.stringify({ book_id: bookId })
            });
            const data = await response.json();
            if (data.success) {
                alert('✓ Buku berhasil ditambahkan ke daftar!');
            } else {
                alert(data.message || '✗ Gagal menambahkan buku');
            }
        };

        document.getElementById('search-btn').addEventListener('click', () => {
            searchBooks(document.getElementById('search-input').value);
        });

        // Enter key to search
        document.getElementById('search-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchBooks(document.getElementById('search-input').value);
            }
        });

        // Initial load
        searchBooks();
    </script>
@endsection