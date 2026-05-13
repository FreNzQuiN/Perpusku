<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perpusku - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-primary: #2563eb;
            --color-secondary: #8b5cf6;
            --color-accent: #06b6d4;
            --color-success: #10b981;
            --color-danger: #ef4444;
            --color-bg: #ffffff;
            --color-surface: #f9fafb;
            --color-text-dark: #1a1a1a;
            --color-text-light: #666666;
            --color-border: #e5e7eb;
        }

        html,
        body {
            height: 100%;
            background-color: var(--color-bg);
            color: var(--color-text-dark);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        /* TOP BAR */
        .topbar {
            height: 60px;
            background-color: var(--color-bg);
            border-bottom: 1px solid var(--color-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 200;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--color-text-dark);
            padding: 8px;
            border-radius: 6px;
            transition: background-color 0.2s;
            display: none;
        }

        .sidebar-toggle:hover {
            background-color: var(--color-surface);
        }

        .topbar-logo {
            font-size: 20px;
            font-weight: 700;
            color: var(--color-text-dark);
            text-decoration: none;
            cursor: pointer;
        }

        .topbar-logo:hover {
            color: var(--color-secondary);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .profile-dropdown {
            position: relative;
        }

        .profile-button {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--color-text-dark);
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .profile-button:hover {
            background-color: var(--color-surface);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: var(--color-bg);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            margin-top: 8px;
            z-index: 300;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 12px 16px;
            color: var(--color-text-dark);
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s;
            border: none;
            background: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .dropdown-menu a:hover {
            background-color: var(--color-surface);
        }

        .dropdown-menu hr {
            margin: 4px 0;
            border: none;
            border-top: 1px solid var(--color-border);
        }

        /* LAYOUT WRAPPER */
        .layout-wrapper {
            display: grid;
            grid-template-columns: 240px 1fr;
            flex: 1;
        }

        /* SIDEBAR */
        .sidebar {
            background-color: var(--color-surface);
            border-right: 1px solid var(--color-border);
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow-y: auto;
        }

        .sidebar-nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--color-text-dark);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background-color: #f3f4f6;
            border-left-color: var(--color-secondary);
        }

        .nav-item.active {
            background-color: #f3f4f6;
            color: var(--color-secondary);
            border-left-color: var(--color-secondary);
        }

        .nav-item .icon {
            font-size: 18px;
        }

        .nav-item .label {
            flex: 1;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--color-border);
        }

        .logout-btn {
            width: 100%;
            padding: 10px 16px;
            background-color: var(--color-danger);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .logout-btn:hover {
            background-color: #dc2626;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }

        .sidebar-backdrop.active {
            display: block;
        }

        /* CONTENT AREA */
        .content-area {
            padding: 24px;
            background-color: var(--color-bg);
            overflow-y: auto;
        }

        .content-area .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* MODERNISASI BOOTSTRAP COMPONENTS */
        .form-control,
        .form-select {
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s;
            background-color: var(--color-bg);
            color: var(--color-text-dark);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #999999;
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--color-text-dark);
            margin-bottom: 6px;
        }

        .card {
            border: 1px solid var(--color-border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            background-color: var(--color-bg);
        }

        .card-header {
            background-color: var(--color-bg);
            border-bottom: 1px solid var(--color-border);
            padding: 16px;
            font-weight: 600;
            color: var(--color-text-dark);
        }

        .card-body {
            padding: 20px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background-color: var(--color-primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-success {
            background-color: var(--color-success);
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: var(--color-danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .btn-outline-primary {
            border: 1px solid var(--color-primary);
            color: var(--color-primary);
            background-color: transparent;
        }

        .btn-outline-primary:hover {
            background-color: rgba(37, 99, 235, 0.1);
        }

        .badge {
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
        }

        .badge.bg-success {
            background-color: var(--color-success) !important;
        }

        .badge.bg-danger {
            background-color: var(--color-danger) !important;
        }

        .badge.bg-primary {
            background-color: var(--color-primary) !important;
        }

        .badge.bg-secondary {
            background-color: var(--color-secondary) !important;
        }

        .table {
            border-collapse: collapse;
            font-size: 14px;
        }

        .table thead {
            background-color: var(--color-surface);
            border-bottom: 2px solid var(--color-border);
        }

        .table th {
            font-weight: 600;
            color: var(--color-text-dark);
            padding: 12px;
            border: none;
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid var(--color-border);
            color: var(--color-text-dark);
        }

        .table tbody tr:hover {
            background-color: var(--color-surface);
        }

        .alert {
            border-radius: 8px;
            border: 1px solid;
            padding: 12px 16px;
            font-size: 14px;
        }

        .alert-info {
            background-color: #dbeafe;
            border-color: #7dd3fc;
            color: #0c4a6e;
        }

        .alert-warning {
            background-color: #fef3c7;
            border-color: #fde68a;
            color: #78350f;
        }

        .alert-success {
            background-color: #d1fae5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            border-color: #fca5a5;
            color: #7f1d1d;
        }

        .error-message {
            color: var(--color-danger);
            font-size: 12px;
            margin-top: 4px;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 1199px) {
            .layout-wrapper {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: fixed;
                left: 0;
                top: 60px;
                width: 240px;
                height: calc(100vh - 60px);
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 100;
                border-right: 1px solid var(--color-border);
                background-color: var(--color-surface);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar-toggle {
                display: block;
            }
        }

        @media (max-width: 767px) {
            .topbar {
                height: 55px;
                padding: 0 16px;
            }

            .topbar-left {
                gap: 12px;
            }

            .topbar-logo {
                font-size: 18px;
            }

            .content-area {
                padding: 16px;
            }

            .content-area .container {
                padding: 0;
            }

            .card-body {
                padding: 16px;
            }

            .btn {
                padding: 8px 12px;
                font-size: 13px;
            }

            .table {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 8px;
            }
        }

        .loading {
            display: none;
        }
    </style>
</head>

<body>
    @auth
        <!-- TOP BAR -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebar-toggle" type="button">☰</button>
                <a href="{{ route('dashboard') }}" class="topbar-logo">Perpusku</a>
            </div>
            <div class="topbar-right">
                <div class="profile-dropdown">
                    <button class="profile-button" id="profile-button" type="button">
                        👤 <span id="user-name-header">User</span>
                    </button>
                    <div class="dropdown-menu" id="profile-dropdown">
                        <a href="{{ route('profile.edit') }}">Profile</a>
                        <hr>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" onclick="confirmLogout(event)">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- LAYOUT WRAPPER -->
        <div class="layout-wrapper">
            <!-- SIDEBAR -->
            <aside class="sidebar" id="sidebar">
                <nav class="sidebar-nav">
                    <a href="{{ route('dashboard') }}" class="nav-item" onclick="setActiveNav(this)">
                        <span class="icon">🏠</span>
                        <span class="label">Dashboard</span>
                    </a>
                    <a href="{{ route('books.index') }}" class="nav-item" onclick="setActiveNav(this)">
                        <span class="icon">📚</span>
                        <span class="label">Cari Buku</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="nav-item" onclick="setActiveNav(this)">
                        <span class="icon">📋</span>
                        <span class="label">Daftar Pinjaman</span>
                    </a>
                </nav>
                <div class="sidebar-footer">
                    <button class="logout-btn" onclick="confirmLogout(event)">Logout</button>
                </div>
            </aside>

            <!-- SIDEBAR BACKDROP (Mobile) -->
            <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

            <!-- CONTENT AREA -->
            <main class="content-area">
                <div class="container">
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        <div style="padding: 40px 20px;">
            @yield('content')
        </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const appRoutes = {
            login: "{{ route('login') }}",
            dashboard: "{{ route('dashboard') }}",
        };
        const apiBaseUrl = "{{ url('/api') }}";

        const resolveApiUrl = (url) => {
            if (url.startsWith('/api/')) {
                return apiBaseUrl + url.slice(4);
            }
            if (url === '/api') {
                return apiBaseUrl;
            }
            return url;
        };

        // Global Fetch Setup
        const apiFetch = async (url, options = {}) => {
            const resolvedUrl = resolveApiUrl(url);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const token = localStorage.getItem('token');
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                ...options.headers
            };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const response = await fetch(resolvedUrl, { ...options, headers });

            if (response.status === 401) {
                localStorage.removeItem('token');
                window.location.href = appRoutes.login;
            }

            return response;
        };

        // Sidebar Toggle
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

        // Close sidebar on link click (mobile)
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth < 1200) {
                    sidebar.classList.remove('active');
                    sidebarBackdrop.classList.remove('active');
                }
            });
        });

        // Profile Dropdown Toggle
        const profileButton = document.getElementById('profile-button');
        const profileDropdown = document.getElementById('profile-dropdown');

        if (profileButton) {
            profileButton.addEventListener('click', () => {
                profileDropdown.classList.toggle('active');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.profile-dropdown')) {
                profileDropdown?.classList.remove('active');
            }
        });

        // Set Active Nav
        function setActiveNav(element) {
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            element.classList.add('active');
        }

        // Initialize on page load
        window.addEventListener('load', () => {
            const currentPath = window.location.pathname;
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                if (item.getAttribute('href') === currentPath) {
                    item.classList.add('active');
                }
            });

            // Load user info
            if (document.getElementById('user-name-header')) {
                apiFetch('/api/user')
                    .then(res => res.json())
                    .then(data => {
                        if (data.name) {
                            document.getElementById('user-name-header').innerText = data.name;
                        }
                    })
                    .catch(err => console.log('User fetch error:', err));
            }
        });

        // Logout with confirmation
        function confirmLogout(event) {
            event.preventDefault();
            if (confirm('Apakah Anda yakin ingin logout?')) {
                // Clear local storage first
                localStorage.removeItem('token');
                // Submit standard form for session logout
                document.getElementById('logout-form').submit();
            }
        }
    </script>
    @yield('scripts')
</body>

</html>