@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .history-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .history-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 18px;
            background: var(--color-bg);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            transition: all 0.2s;
        }

        .history-item:hover {
            border-color: #d1d5db;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .history-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .history-icon svg {
            width: 20px;
            height: 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .history-info {
            flex: 1;
            min-width: 0;
        }

        .history-info .h-date {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .history-info .h-books {
            font-size: 12px;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .history-info .h-books span {
            display: inline-block;
            background: #f3f4f6;
            color: #374151;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 4px;
            margin: 2px 4px 2px 0;
        }

        .history-dur {
            flex-shrink: 0;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: #eef2ff;
            color: #6366f1;
        }

        .hero-card {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .hero-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 28px 24px;
        }

        .hero-card .card-header h3 {
            color: #fff;
            margin-bottom: 4px;
            font-size: 22px;
            font-weight: 700;
        }

        .hero-card .card-header p {
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 0;
            font-size: 14px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 16px;
        }

        .section-header svg {
            color: var(--color-secondary);
        }
    </style>

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

@section('scripts')
    <script>
        const loadHistory = async () => {
            const response = await apiFetch('/api/my-borrowings');
            if (response.status === 401) return;
            const data = await response.json();
            const container = document.getElementById('history-container');

            if (data.success && data.data.length > 0) {
                let html = '<div class="history-list">';
                data.data.forEach(item => {
                    const books = item.details.map(d =>
                        `<span>${esc(d.book.title)}</span>`
                    ).join(' ');
                    const borrowDate = new Date(item.borrow_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
                    html += `
                        <div class="history-item">
                            <div class="history-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                            </div>
                            <div class="history-info">
                                <div class="h-date">${esc(borrowDate)}</div>
                                <div class="h-books">${books}</div>
                            </div>
                            <div class="history-dur">${esc(item.duration_days)} Hari</div>
                        </div>`;
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div style="text-align: center; padding: 40px 20px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 12px;">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/>
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17"/>
                        </svg>
                        <h5 style="font-size: 15px; font-weight: 600; color: #374151; margin: 0 0 4px;">Belum ada riwayat</h5>
                        <p style="font-size: 13px; color: #9ca3af; margin: 0;">
                            <a href="{{ route('books.index') }}" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">Mulai cari buku</a> untuk meminjam
                        </p>
                    </div>`;
            }
        };

        loadHistory();
    </script>
@endsection

@section('footer')
    <footer class="footer-text">
        &copy; {{ date('Y') }} Perpusku. All rights reserved.
    </footer>
@endsection
