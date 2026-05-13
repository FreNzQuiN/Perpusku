@extends('layouts.app')

@section('title', 'Daftar Pinjaman')

@section('content')
    <div>
        <div class="card">
            <div class="card-header">
                <h4 style="margin-bottom: 0; font-weight: 600;">📋 Daftar Pinjaman Sementara</h4>
            </div>
            <div class="card-body">
                <div id="cart-container" style="margin-top: 0;">
                    <div style="text-align: center; color: #666666; padding: 20px;">Memuat daftar...</div>
                </div>
            </div>
            <div id="action-container"
                style="display: none; padding: 16px 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Tambah Buku Lagi</a>
                <a href="{{ route('borrow.confirm') }}" class="btn btn-primary">Lanjut Konfirmasi</a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const loadCart = async () => {
            const response = await apiFetch('/api/cart');
            const data = await response.json();
            const container = document.getElementById('cart-container');
            const actions = document.getElementById('action-container');

            if (data.success && data.data.length > 0) {
                let html = '<table class="table table-hover"><thead><tr><th>Judul</th><th>Penulis</th><th>Aksi</th></tr></thead><tbody>';
                data.data.forEach(item => {
                    html += `<tr><td>${item.book.title}</td><td>${item.book.author}</td><td><button onclick="removeFromCart(${item.id})" class="btn btn-sm btn-danger">Hapus</button></td></tr>`;
                });
                html += '</tbody></table>';
                container.innerHTML = html;
                actions.style.display = 'block';
            } else {
                container.innerHTML = '<div class="alert alert-info">Keranjang Anda masih kosong. <a href="{{ route('books.index') }}">Cari buku sekarang.</a></div>';
                actions.style.display = 'none';
            }
        };

        const removeFromCart = async (id) => {
            if (!confirm('Hapus buku ini dari daftar?')) return;
            const response = await apiFetch(`/api/cart/${id}`, { method: 'DELETE' });
            const data = await response.json();
            if (data.success) {
                loadCart();
            }
        };

        loadCart();
    </script>
@endsection