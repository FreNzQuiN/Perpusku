@extends('layouts.app')

@section('title', 'Konfirmasi Peminjaman')

@section('content')
    <style>
        .summary-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: #f9fafb;
            border: 1px solid var(--color-border);
            border-radius: 10px;
        }

        .summary-item .s-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-primary);
            flex-shrink: 0;
        }

        .summary-item .s-title {
            flex: 1;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .summary-item .s-qty {
            font-size: 11px;
            font-weight: 700;
            color: #6366f1;
            background: #eef2ff;
            padding: 2px 10px;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            border-top: 1px solid var(--color-border);
            margin-top: 4px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .form-actions .btn {
            flex: 1;
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>

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

@section('scripts')
    <script>
        let bookIds = [];

        const loadSummary = async () => {
            const response = await apiFetch('/api/cart');
            if (response.status === 401) return;
            const data = await response.json();
            const container = document.getElementById('summary-container');

            if (data.success && data.data.length > 0) {
                bookIds = data.data.map(item => item.book_id);
                let html = '<div class="summary-list">';
                data.data.forEach(item => {
                    html += `
                        <div class="summary-item">
                            <div class="s-dot"></div>
                            <div class="s-title">${esc(item.book.title)}</div>
                            <div class="s-qty">1</div>
                        </div>`;
                });
                html += `</div>
                    <div class="summary-total">
                        <span>Total Buku</span>
                        <span>${bookIds.length}</span>
                    </div>`;
                container.innerHTML = html;

                if (bookIds.length > 10) {
                    document.getElementById('error-duration').innerHTML = 'Maksimal 10 buku dalam satu transaksi!';
                    document.getElementById('submit-btn').disabled = true;
                }
            } else {
                window.location.href = "{{ route('cart.index') }}";
            }
        };

        document.getElementById('confirm-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerText = 'Memproses...';

            const response = await apiFetch('/api/borrowings', {
                method: 'POST',
                body: JSON.stringify({
                    borrow_date: document.getElementById('borrow_date').value,
                    duration_days: document.getElementById('duration').value,
                    book_ids: bookIds
                })
            });

            const data = await response.json();

            if (response.ok) {
                showNotification('✓ Peminjaman Berhasil!');
                setTimeout(() => {
                    window.location.href = "{{ route('dashboard') }}";
                }, 1000);
            } else {
                btn.disabled = false;
                btn.innerText = 'Konfirmasi Pinjam';
                if (data.errors) {
                    if (data.errors.duration_days) document.getElementById('error-duration').innerText = data.errors.duration_days[0];
                    if (data.errors.borrow_date) document.getElementById('error-date').innerText = data.errors.borrow_date[0];
                    if (data.errors.book_ids) showNotification(data.errors.book_ids[0], 'error');
                } else {
                    showNotification(data.message || 'Gagal melakukan peminjaman', 'error');
                }
            }
        });

        loadSummary();
    </script>
@endsection
