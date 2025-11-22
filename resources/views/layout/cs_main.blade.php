<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ROMS CS Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* =================================================
        INI ADALAH STYLE YANG SAMA DENGAN LAYOUT ADMIN
        (Palet warna Anda: Hijau, Maroon, Emas)
        =================================================
        */
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #84994F; /* HIJAU ANDA */
            --sidebar-link: #eaf0dc; 
            --sidebar-link-hover: #ffffff;
            --sidebar-link-active: #FCB53B; /* EMAS ANDA */
            --topbar-height: 70px;
            --primary-maroon: #B45253; /* MAROON ANDA */
        }
        
        body { background-color: #f8f9fa; }
        .main-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            padding: 20px; overflow-y: auto; color: white;
        }
        .main-sidebar .logo {
            font-size: 1.8rem; font-weight: 700; color: var(--sidebar-link-hover);
            text-decoration: none; display: block; margin-bottom: 25px;
        }
        .main-sidebar .user-profile {
            display: flex; align-items: center; padding: 10px;
            background-color: rgba(255,255,255, 0.1);
            border-radius: 10px; margin-bottom: 20px;
        }
        .main-sidebar .user-profile .avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background-color: var(--sidebar-link-active); 
            color: var(--primary-maroon);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; margin-right: 12px;
        }
        .main-sidebar .user-profile .username { font-weight: 600; font-size: 0.95rem; }
        .main-sidebar .nav-link {
            color: var(--sidebar-link); font-size: 0.95rem; padding: 10px 15px;
            border-radius: 8px; margin-bottom: 4px; display: flex;
            align-items: center; gap: 12px;
        }
        .main-sidebar .nav-link i { font-size: 1.2rem; }
        .main-sidebar .nav-link:hover {
            color: var(--sidebar-link-hover);
            background-color: rgba(255,255,255, 0.15);
        }
        .main-sidebar .nav-link.active { color: var(--sidebar-link-active); font-weight: 600; }
        .main-sidebar .nav-link:focus { outline: none; }
        .main-topbar {
            position: fixed; top: 0; left: var(--sidebar-width); right: 0;
            height: var(--topbar-height); background-color: #ffffff;
            border-bottom: 1px solid #dee2e6; padding: 0 30px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .main-topbar .input-group { width: 400px; }
        .main-topbar .topbar-nav .btn { margin-left: 10px; }
        .btn-primary {
            background-color: var(--primary-maroon);
            border-color: var(--primary-maroon);
        }
        .btn-primary:hover { background-color: #9a4243; border-color: #9a4243; }
        .main-content {
            margin-left: var(--sidebar-width); margin-top: var(--topbar-height);
            padding: 30px; min-height: calc(100vh - var(--topbar-height));
        }
        @media (max-width: 992px) {
            .main-sidebar { left: -100%; z-index: 1050; transition: left 0.3s ease; }
            .main-sidebar.show { left: 0; }
            .main-topbar { left: 0; padding: 0 15px; }
            .main-topbar .input-group { width: 100%; max-width: 300px; }
            .main-content { margin-left: 0; padding: 20px 15px; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1040; }
            .sidebar-overlay.show { display: block; }
            .page-header { padding: 20px !important; }
            .page-header h3 { font-size: 1.25rem !important; }
        }
        @media (max-width: 576px) {
            .main-topbar .input-group { display: none; }
            .page-header { flex-direction: column !important; align-items: flex-start !important; }
            .page-header .btn { width: 100%; margin-top: 15px; }
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="main-sidebar">
        
        <a href="{{ route('chat.dashboard') }}" class="logo">ROMS (CS)</a>

        <div class="user-profile">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div> <div>
                <span class="username">{{ auth()->user()->name }}</span>
                <small class="d-block text-muted">{{ ucfirst(auth()->user()->role) }}</small>
            </div>
        </div>
        
        <ul class="nav flex-column">
            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('cs/dashboard') ? 'active' : '' }}" href="{{ route('chat.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Beranda CS
                </a>
            </li>

            {{-- Divider --}}
            <li class="nav-item mt-2 mb-2">
                <hr class="sidebar-divider" style="border-color: rgba(255,255,255,0.2); margin: 0;">
            </li>
            <li class="nav-item">
                <small class="text-muted ps-3" style="font-size: 0.75rem; opacity: 0.7;">LAYANAN</small>
            </li>

            {{-- Service Menu --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('app/chat*') ? 'active' : '' }}" href="{{ route('chat.index') }}">
                    <i class="bi bi-chat-dots-fill"></i> Chat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('cs/obrolan*') ? 'active' : '' }}" href="{{ route('cs.obrolan') }}">
                    <i class="bi bi-chat-text-fill"></i> Obrolan
                </a>
            </li>

            {{-- Divider --}}
            <li class="nav-item mt-2 mb-2">
                <hr class="sidebar-divider" style="border-color: rgba(255,255,255,0.2); margin: 0;">
            </li>
            <li class="nav-item">
                <small class="text-muted ps-3" style="font-size: 0.75rem; opacity: 0.7;">DATA</small>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('app/customers*') || request()->is('cs/pelanggan*') ? 'active' : '' }}" href="{{ route('customer.index') }}">
                    <i class="bi bi-people-fill"></i> Pelanggan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('app/orders*') || request()->is('cs/pesanan*') ? 'active' : '' }}" href="{{ route('order.index') }}">
                    <i class="bi bi-receipt-cutoff"></i> Pesanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('app/products*') ? 'active' : '' }}" href="{{ route('product.index') }}">
                    <i class="bi bi-box-seam-fill"></i> Produk
                </a>
            </li>

            <li class="nav-item mt-auto pt-3 border-top border-secondary">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link w-100 border-0 text-start" style="background: none;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>

    </aside>

    <main>
        <nav class="main-topbar">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Cari...">
                </div>
            </div>
            <div class="topbar-nav d-none d-md-flex">
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i><span class="d-none d-lg-inline">Buat Pesanan</span>
                </button>
            </div>
        </nav>

        <div class="main-content">
            @yield('main-content') 
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.main-sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>
    @stack('styles')
    @stack('scripts')
</body>
</html>