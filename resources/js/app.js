import 'bootstrap';

const esc = (str) => {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str ?? ''));
    return div.innerHTML;
};

const appRoutes = {
    login: document.querySelector('meta[name="app-login-url"]')?.getAttribute('content') || '/login',
    dashboard: document.querySelector('meta[name="app-dashboard-url"]')?.getAttribute('content') || '/dashboard',
};
const apiBaseUrl = document.querySelector('meta[name="api-base-url"]')?.getAttribute('content') || '/api';

const resolveApiUrl = (url) => {
    if (url.startsWith('/api/')) {
        return apiBaseUrl + url.slice(4);
    }
    if (url === '/api') {
        return apiBaseUrl;
    }
    return url;
};

const getToken = () => localStorage.getItem('token');

const apiFetch = async (url, options = {}) => {
    const resolvedUrl = resolveApiUrl(url);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const token = getToken();
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
        ...options.headers
    };
    if (token) headers['Authorization'] = `Bearer ${token}`;

    const response = await fetch(resolvedUrl, { ...options, headers });

    if (response.status === 401 && !options.noRedirect) {
        localStorage.removeItem('token');
    }

    return response;
};

const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebar = document.getElementById('sidebar');
const sidebarBackdrop = document.getElementById('sidebar-backdrop');

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        sidebarBackdrop.classList.toggle('active');
    });
}

if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', () => {
        sidebar.classList.remove('active');
        sidebarBackdrop.classList.remove('active');
    });
}

const navItems = document.querySelectorAll('.nav-item');
navItems.forEach(item => {
    item.addEventListener('click', () => {
        if (window.innerWidth < 1200) {
            sidebar.classList.remove('active');
            sidebarBackdrop.classList.remove('active');
        }
    });
});

const profileButton = document.getElementById('profile-button');
const profileDropdown = document.getElementById('profile-dropdown');

if (profileButton) {
    profileButton.addEventListener('click', () => {
        profileDropdown.classList.toggle('active');
    });
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.profile-dropdown')) {
        profileDropdown?.classList.remove('active');
    }
});

function setActiveNav(element) {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    element.classList.add('active');
}

window.addEventListener('load', () => {
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        if (item.getAttribute('href') === currentPath) {
            item.classList.add('active');
        }
    });
});

function showConfirmDialog(message) {
    return new Promise(resolve => {
        const overlay = document.createElement('div');
        overlay.className = 'confirm-overlay';
        overlay.innerHTML = `
            <div class="confirm-box">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div class="message">${message}</div>
                <div class="actions">
                    <button class="btn-cancel" id="confirm-cancel">Batal</button>
                    <button class="btn-confirm" id="confirm-ok">Ya, Saya Yakin</button>
                </div>
            </div>`;

        document.body.appendChild(overlay);
        requestAnimationFrame(() => overlay.classList.add('active'));

        const done = (result) => {
            document.removeEventListener('keydown', onKeydown);
            overlay.classList.remove('active');
            setTimeout(() => overlay.remove(), 200);
            resolve(result);
        };

        const onKeydown = (e) => {
            if (e.key === 'Escape') done(false);
        };

        overlay.querySelector('#confirm-ok').onclick = () => done(true);
        overlay.querySelector('#confirm-cancel').onclick = () => done(false);
        overlay.onclick = (e) => { if (e.target === overlay) done(false); };
        document.addEventListener('keydown', onKeydown);
        overlay.querySelector('#confirm-ok').focus();
    });
}

function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

const BOOK_SVG = `<svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17"/>
    <line x1="8" y1="7" x2="16" y2="7"/>
    <line x1="8" y1="11" x2="14" y2="11"/>
    <circle cx="18" cy="6" r="0.5" fill="currentColor"/>
</svg>`;

function confirmLogout(event) {
    event.preventDefault();
    showConfirmDialog('Apakah Anda yakin ingin logout?').then(ok => {
        if (ok) {
            localStorage.removeItem('token');
            document.getElementById('logout-form').submit();
        }
    });
}
window.confirmLogout = confirmLogout;
window.setActiveNav = setActiveNav;

if (document.getElementById('results-container')) {
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
    window.addToCart = addToCart;

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
    window.removeFromCartSearch = removeFromCartSearch;

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-input');
        const searchBtn = document.getElementById('search-btn');

        searchBtn.addEventListener('click', () => searchBooks(searchInput.value));
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') searchBooks(searchInput.value);
        });

        searchBooks();
    });
}

if (document.getElementById('history-container')) {
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
            const searchUrl = document.querySelector('meta[name="books-index-url"]')?.getAttribute('content') || '/search-books';
            container.innerHTML = `
                <div style="text-align: center; padding: 40px 20px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 12px;">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/>
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17"/>
                    </svg>
                    <h5 style="font-size: 15px; font-weight: 600; color: #374151; margin: 0 0 4px;">Belum ada riwayat</h5>
                    <p style="font-size: 13px; color: #9ca3af; margin: 0;">
                        <a href="${searchUrl}" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">Mulai cari buku</a> untuk meminjam
                    </p>
                </div>`;
        }
    };

    loadHistory();
}

if (document.getElementById('cart-container')) {
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
            const searchUrl = document.querySelector('meta[name="books-index-url"]')?.getAttribute('content') || '/search-books';
            container.innerHTML = `
                <div class="empty-cart">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                    </svg>
                    <h5>Daftar pinjaman masih kosong</h5>
                    <p><a href="${searchUrl}" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">Cari buku</a> untuk menambah pinjaman</p>
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
    window.removeFromCart = removeFromCart;

    loadCart();
}

if (document.getElementById('summary-container')) {
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
            const cartUrl = document.querySelector('meta[name="cart-index-url"]')?.getAttribute('content') || '/manage-cart';
            window.location.href = cartUrl;
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
                const dashboardUrl = document.querySelector('meta[name="app-dashboard-url"]')?.getAttribute('content') || '/dashboard';
                window.location.href = dashboardUrl;
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
}
