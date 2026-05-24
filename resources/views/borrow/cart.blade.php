@extends('layouts.app')

@section('title', 'Daftar Pinjaman')

@section('content')
    <style>
        .cart-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: var(--color-bg);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            transition: all 0.2s;
        }

        .cart-item:hover {
            border-color: #d1d5db;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .cart-cover {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .cart-cover svg {
            width: 20px;
            height: 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .cart-info {
            flex: 1;
            min-width: 0;
        }

        .cart-info .c-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-info .c-author {
            font-size: 12px;
            color: #9ca3af;
        }

        .btn-remove {
            flex-shrink: 0;
            padding: 8px 14px;
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #dc2626;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-remove:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .btn-remove svg {
            width: 14px;
            height: 14px;
        }

        .cart-actions {
            display: flex;
            gap: 10px;
            padding: 16px 20px;
            border-top: 1px solid var(--color-border);
            justify-content: flex-end;
        }

        .cart-actions .btn {
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .empty-cart {
            text-align: center;
            padding: 48px 20px;
        }

        .empty-cart svg {
            color: #d1d5db;
            margin-bottom: 12px;
        }

        .empty-cart h5 {
            font-size: 15px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 4px;
        }

        .empty-cart p {
            font-size: 13px;
            color: #9ca3af;
            margin: 0;
        }
    </style>

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

@section('scripts')
    <script>
        const loadCart = async () => {
            const response = await apiFetch('/api/cart');
            if (response.status === 401) return;
            const data = await response.json();
            const container = document.getElementById('cart-container');
            const actions = document.getElementById('action-container');

            if (data.success && data.data.length > 0) {
                let html = '<div class="cart-list">';
                data.data.forEach(item => {
                    html += `
                        <div class="cart-item">
                            <div class="cart-cover">${BOOK_SVG}</div>
                            <div class="cart-info">
                                <div class="c-title">${esc(item.book.title)}</div>
                                <div class="c-author">${esc(item.book.author)}</div>
                            </div>
                            <button onclick="removeFromCart(${item.id})" class="btn-remove">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                                Hapus
                            </button>
                        </div>`;
                });
                html += '</div>';
                container.innerHTML = html;
                actions.style.display = 'flex';
            } else {
                container.innerHTML = `
                    <div class="empty-cart">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                        </svg>
                        <h5>Daftar pinjaman masih kosong</h5>
                        <p><a href="{{ route('books.index') }}" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">Cari buku</a> untuk menambah pinjaman</p>
                    </div>`;
                actions.style.display = 'none';
            }
        };

        const removeFromCart = async (id) => {
            const ok = await showConfirmDialog('Hapus buku ini dari daftar pinjaman?');
            if (!ok) return;
            const response = await apiFetch(`/api/cart/${id}`, { method: 'DELETE' });
            const data = await response.json();
            if (data.success) {
                showNotification('✓ Buku berhasil dihapus dari daftar');
                loadCart();
            } else {
                showNotification(data.message || '✗ Gagal menghapus buku', 'error');
            }
        };

        loadCart();
    </script>
@endsection

@section('footer')
    <footer class="footer-text">
        &copy; {{ date('Y') }} Perpusku. All rights reserved.
    </footer>
@endsection
