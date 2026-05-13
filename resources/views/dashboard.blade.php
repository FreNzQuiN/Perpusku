@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div>
        <div class="card">
            <div class="card-header"
                style="background-color: linear-gradient(135deg, #8b5cf6 0%, #06b6d4 100%); border: none;">
                <h3 style="color: white; margin-bottom: 4px; font-size: 28px;">Selamat Datang di Perpusku 👋</h3>
                <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 0; font-size: 14px;">Gunakan menu di samping untuk
                    mencari buku atau kelola pinjaman Anda</p>
            </div>
            <div class="card-body">
                <h5 style="font-weight: 600; margin-bottom: 16px;">📖 Riwayat Peminjaman Terakhir</h5>
                <div id="history-container" style="margin-top: 16px;">
                    <div style="text-align: center; color: #666666; padding: 20px;">Memuat riwayat...</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const loadHistory = async () => {
            const response = await apiFetch('/api/my-borrowings');
            const data = await response.json();
            const container = document.getElementById('history-container');

            if (data.success && data.data.length > 0) {
                let html = '<table class="table"><thead><tr style="background-color: #f9fafb;"><th style="font-weight: 600;">Tanggal Pinjam</th><th style="font-weight: 600;">Durasi</th><th style="font-weight: 600;">Buku</th></tr></thead><tbody>';
                data.data.forEach(item => {
                    const books = item.details.map(d => d.book.title).join(', ');
                    const borrowDate = new Date(item.borrow_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
                    html += `<tr><td>${borrowDate}</td><td><span class="badge bg-secondary">${item.duration_days} Hari</span></td><td>${books}</td></tr>`;
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="alert alert-info" style="margin-bottom: 0;">📚 Belum ada riwayat peminjaman. <a href="{{ route('books.index') }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">Mulai cari buku sekarang</a></div>';
            }
        };

        loadHistory();
    </script>
@endsection