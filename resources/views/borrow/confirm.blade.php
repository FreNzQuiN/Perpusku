@extends('layouts.app')

@section('title', 'Konfirmasi Peminjaman')

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h4 style="margin-bottom: 0; font-weight: 600;">✅ Konfirmasi Peminjaman</h4>
            </div>
            <div class="card-body">
                <div id="summary-container" style="margin-bottom: 24px;">
                    <!-- Ringkasan buku -->
                </div>
                <form id="confirm-form">
                    <div class="mb-3">
                        <label class="form-label">Lama Peminjaman (Maksimal 3 Hari)</label>
                        <input type="number" id="duration" class="form-control" min="1" max="3" value="1" required>
                        <div id="error-duration" class="error-message"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" id="borrow_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        <div id="error-date" class="error-message"></div>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <a href="{{ route('cart.index') }}" class="btn btn-secondary flex-grow-1">Kembali</a>
                        <button type="submit" id="submit-btn" class="btn btn-primary flex-grow-1"
                            style="background-color: #10b981;">Konfirmasi Pinjam</button>
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
            const data = await response.json();
            const container = document.getElementById('summary-container');

            if (data.success && data.data.length > 0) {
                bookIds = data.data.map(item => item.book_id);
                let html = '<ul class="list-group">';
                data.data.forEach(item => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">${item.book.title} <span class="badge bg-primary rounded-pill">1</span></li>`;
                });
                html += `</ul><div class="mt-2 text-end"><strong>Total: ${bookIds.length} Buku</strong></div>`;
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
                alert('Peminjaman Berhasil!');
                window.location.href = "{{ route('dashboard') }}";
            } else {
                btn.disabled = false;
                btn.innerText = 'Konfirmasi Pinjam';
                if (data.errors) {
                    if (data.errors.duration_days) document.getElementById('error-duration').innerText = data.errors.duration_days[0];
                    if (data.errors.borrow_date) document.getElementById('error-date').innerText = data.errors.borrow_date[0];
                    if (data.errors.book_ids) alert(data.errors.book_ids[0]);
                } else {
                    alert(data.message || 'Gagal melakukan peminjaman');
                }
            }
        });

        loadSummary();
    </script>
@endsection