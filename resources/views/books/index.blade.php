@extends('layouts.app')

@section('title', 'Cari Buku')

@section('content')
    <style>
        .card-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 24px;
        }

        .book-card {
            display: flex;
            flex-direction: column;
            background: var(--color-bg);
            border: 1px solid var(--color-border);
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-color: #d1d5db;
        }

        .book-cover {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 10;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .book-cover svg {
            width: 56px;
            height: 56px;
            opacity: 0.4;
            color: #fff;
        }

        .book-cover .stock-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            border: none;
            backdrop-filter: blur(4px);
        }

        .stock-badge.available {
            background: rgba(16, 185, 129, 0.9);
            color: #fff;
        }

        .stock-badge.unavailable {
            background: rgba(239, 68, 68, 0.9);
            color: #fff;
        }

        .book-card-body {
            padding: 16px 18px 18px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .book-title {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.4;
            color: #111827;
            margin: 0 0 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.8em;
        }

        .book-author {
            font-size: 13px;
            color: #6b7280;
            margin: 0 0 12px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-actions {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .borrowing-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #6366f1;
            background: #eef2ff;
            padding: 8px 12px;
            border-radius: 8px;
            line-height: 1.3;
        }

        .borrowing-info svg {
            flex-shrink: 0;
        }

        .btn-add {
            width: 100%;
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-add.primary {
            background: var(--color-primary);
            color: #fff;
        }

        .btn-add.primary:hover:not(:disabled) {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-add.primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-add.danger-soft {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-add.danger-soft:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .btn-add.secondary-disabled {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .search-wrapper {
            display: flex;
            gap: 10px;
        }

        .search-input-wrap {
            flex: 1;
            position: relative;
        }

        .search-input-wrap svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .search-input-wrap input {
            width: 100%;
            padding: 11px 14px 11px 42px;
            border: 1px solid var(--color-border);
            border-radius: 10px;
            font-size: 14px;
            background: var(--color-bg);
            color: var(--color-text-dark);
            transition: all 0.2s;
            outline: none;
        }

        .search-input-wrap input:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .search-input-wrap input::placeholder {
            color: #9ca3af;
        }

        .btn-search {
            padding: 11px 24px;
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-search:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .loading-skeleton {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 24px;
        }

        .skeleton-card {
            background: var(--color-bg);
            border: 1px solid var(--color-border);
            border-radius: 14px;
            overflow: hidden;
        }

        .skeleton-cover {
            width: 100%;
            aspect-ratio: 16 / 10;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        .skeleton-body {
            padding: 16px 18px 18px;
        }

        .skeleton-line {
            height: 14px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .skeleton-line.short {
            width: 60%;
        }

        .skeleton-line.btn {
            height: 40px;
            margin-bottom: 0;
            margin-top: 12px;
            border-radius: 10px;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .empty-state {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state svg {
            color: #d1d5db;
            margin-bottom: 16px;
        }

        .empty-state h5 {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 6px;
        }

        .empty-state p {
            font-size: 13px;
            color: #9ca3af;
            margin: 0;
        }

        @media (max-width: 1199px) {
            .card-grid,
            .loading-skeleton {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 767px) {
            .card-grid,
            .loading-skeleton {
                grid-template-columns: 1fr;
            }

            .search-wrapper {
                flex-direction: column;
            }

            .btn-search {
                width: 100%;
                justify-content: center;
            }

            .book-cover {
                aspect-ratio: 16 / 9;
            }
        }
    </style>

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

@section('scripts')
    <script>
        const showSkeleton = () => {
            const container = document.getElementById('results-container');
            container.style.display = 'grid';
            container.innerHTML = Array.from({ length: 6 }, () =>
                `<div class="skeleton-card">
                    <div class="skeleton-cover"></div>
                    <div class="skeleton-body">
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line short"></div>
                        <div class="skeleton-line btn"></div>
                    </div>
                </div>`
            ).join('');
        };

        const searchBooks = async (title = '') => {
            const results = document.getElementById('results-container');
            showSkeleton();

            const response = await apiFetch(`/api/books?title=${encodeURIComponent(title)}`);
            if (response.status === 401) return;
            const result = await response.json();
            const books = result.data || result;

            if (books.length > 0) {
                results.innerHTML = books.map(book => {
                    let actionHtml = '';

                    if (book.in_borrowing) {
                        const returnDate = new Date(book.borrowing.return_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
                        const borrowDate = new Date(book.borrowing.borrow_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
                        actionHtml = `
                            <div class="borrowing-info">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <span>Dipinjam <strong>${esc(borrowDate)}</strong> s.d. <strong>${esc(returnDate)}</strong></span>
                            </div>
                            <button class="btn-add secondary-disabled" disabled>Sedang Dipinjam</button>`;
                    } else if (book.in_cart) {
                        actionHtml = `
                            <button onclick="removeFromCartSearch(${book.id}, ${book.cart_id})" class="btn-add danger-soft">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                                Hapus dari Keranjang
                            </button>`;
                    } else {
                        const disabled = book.stock <= 0;
                        actionHtml = `
                            <button onclick="addToCart(${book.id})" class="btn-add primary" ${disabled ? 'disabled' : ''}>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                ${disabled ? 'Stok Habis' : 'Tambah Pinjaman'}
                            </button>`;
                    }

                    const stockClass = book.stock > 0 ? 'available' : 'unavailable';

                    return `<div class="book-card">
                            <div class="book-cover">
                                ${BOOK_SVG}
                                <span class="stock-badge ${stockClass}">Stok ${esc(book.stock)}</span>
                            </div>
                            <div class="book-card-body">
                                <div class="book-title">${esc(book.title)}</div>
                                <div class="book-author">${esc(book.author)}</div>
                                <div class="book-actions">${actionHtml}</div>
                            </div>
                        </div>`;
                }).join('');
            } else {
                results.innerHTML = `
                    <div class="empty-state">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17"/>
                        </svg>
                        <h5>${title ? 'Buku tidak ditemukan' : 'Belum ada pencarian'}</h5>
                        <p>${title ? 'Coba gunakan kata kunci lain untuk mencari buku.' : 'Silakan masukkan judul buku yang ingin dicari.'}</p>
                    </div>`;
            }
        };

        const addToCart = async (bookId) => {
            const response = await apiFetch('/api/cart', {
                method: 'POST',
                body: JSON.stringify({ book_id: bookId })
            });
            const data = await response.json();
            if (data.success) {
                showNotification('✓ Buku berhasil ditambahkan ke daftar pinjaman!');
                searchBooks(document.getElementById('search-input').value);
            } else {
                showNotification(data.message || '✗ Gagal menambahkan buku', 'error');
            }
        };

        const removeFromCartSearch = async (bookId, cartId) => {
            const ok = await showConfirmDialog('Hapus buku ini dari keranjang?');
            if (!ok) return;
            const response = await apiFetch(`/api/cart/${cartId}`, { method: 'DELETE' });
            const data = await response.json();
            if (data.success) {
                showNotification('✓ Buku berhasil dihapus dari keranjang');
                searchBooks(document.getElementById('search-input').value);
            } else {
                showNotification(data.message || '✗ Gagal menghapus buku', 'error');
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search-input');
            const searchBtn = document.getElementById('search-btn');

            searchBtn.addEventListener('click', () => searchBooks(searchInput.value));
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') searchBooks(searchInput.value);
            });

            searchBooks();
        });
    </script>
@endsection

@section('footer')
    <footer class="footer-text">
        &copy; {{ date('Y') }} Perpusku. All rights reserved.
    </footer>
@endsection