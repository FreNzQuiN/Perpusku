<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-base-url" content="{{ url('/api') }}">
    <meta name="app-login-url" content="{{ route('login') }}">
    <meta name="app-dashboard-url" content="{{ route('dashboard') }}">
    <meta name="books-index-url" content="{{ route('books.index') }}">
    <meta name="cart-index-url" content="{{ route('cart.index') }}">
    <title>Perpusku - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="notification-container"></div>
    @auth
        <!-- TOP BAR -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebar-toggle" type="button">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="21" x2="21" y2="21"/>
                            </svg>
                        </button>
                <a href="{{ route('dashboard') }}" class="topbar-logo">Perpusku</a>
            </div>
            <div class="topbar-right">
                <div class="profile-dropdown">
                    <button class="profile-button" id="profile-button" type="button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> <span id="user-name-header">{{ Auth::user()->name }}</span>
                    </button>
                    <div class="dropdown-menu" id="profile-dropdown">
                        <a href="{{ route('profile.edit') }}">Profil</a>
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
                        <span class="icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </span>
                        <span class="label">Dashboard</span>
                    </a>
                    <a href="{{ route('books.index') }}" class="nav-item" onclick="setActiveNav(this)">
                        <span class="icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17"/></svg>
                        </span>
                        <span class="label">Cari Buku</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="nav-item" onclick="setActiveNav(this)">
                        <span class="icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
                        </span>
                        <span class="label">Daftar Pinjaman</span>
                    </a>
                </nav>
                <div class="sidebar-footer">
                    <button class="logout-btn" onclick="confirmLogout(event)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </button>
                </div>
            </aside>

            <!-- SIDEBAR BACKDROP (Mobile) -->
            <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

            <!-- CONTENT AREA -->
            <main class="content-area">
                <div class="container">
                    @yield('content')
                    <footer class="footer-text">
                        &copy; {{ date('Y') }} Perpusku. Hak cipta dilindungi undang-undang.
                    </footer>
                </div>
            </main>
        </div>
    @else
        <div style="padding: 40px 20px;">
            @yield('content')
        </div>
    @endauth


    @yield('scripts')
</body>

</html>