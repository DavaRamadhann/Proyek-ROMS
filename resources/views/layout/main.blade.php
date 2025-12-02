<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ROMS Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #84994F; /* HIJAU ANDA */
            --sidebar-link: #eaf0dc; /* Putih/Krem (turunan hijau) */
            --sidebar-link-hover: #ffffff;
            --sidebar-link-active: #FCB53B; /* EMAS ANDA */
            --topbar-height: 72px;
            --primary-maroon: #B45253; /* MAROON ANDA */
            --body-bg: #f1f5f9;
            --primary-brand: #84994F;
        }
        
        body {
            background-color: var(--body-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* SIDEBAR ORIGINAL (GREEN) */
        .main-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            padding: 20px;
            overflow-y: auto;
            color: white;
            z-index: 1050;
            transition: left 0.3s ease;
        }

        .main-sidebar .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--sidebar-link-hover);
            text-decoration: none;
            display: block;
            margin-bottom: 25px;
        }

        .main-sidebar .user-profile {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: rgba(255,255,255, 0.1);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .main-sidebar .user-profile .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--sidebar-link-active); /* Emas */
            color: var(--primary-maroon); /* Teks Maroon */
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 12px;
        }
        .main-sidebar .user-profile .username {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .main-sidebar .nav-link {
            color: var(--sidebar-link);
            font-size: 0.95rem;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .main-sidebar .nav-link i {
            font-size: 1.2rem;
        }
        .main-sidebar .nav-link:hover {
            color: var(--sidebar-link-hover);
            background-color: rgba(255,255,255, 0.15);
        }
        .main-sidebar .nav-link.active {
            color: var(--sidebar-link-active); /* Emas */
            font-weight: 600;
        }
        .main-sidebar .nav-link:focus {
            outline: none;
        }

        /* TOPBAR PREMIUM (KEPT) */
        .main-topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.9); /* Glassmorphism base */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1040;
            transition: all 0.3s ease;
        }

        /* OMNIBAR SEARCH */
        .omnibar {
            background-color: #f1f5f9;
            border: 1px solid transparent;
            border-radius: 99px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            width: 320px;
            transition: all 0.2s ease;
        }
        .omnibar:focus-within {
            background-color: white;
            border-color: var(--primary-brand);
            box-shadow: 0 0 0 4px rgba(132, 153, 79, 0.1);
            width: 350px; /* Expand effect */
        }
        .omnibar input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 0.9rem;
            color: #334155;
            margin-left: 8px;
        }
        .omnibar i {
            color: #94a3b8;
        }

        /* PULSE ANIMATION FOR ONLINE STATUS */
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
        .status-pulse {
            animation: pulse-green 2s infinite;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 32px; 
            min-height: calc(100vh - var(--topbar-height));
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .main-sidebar { left: -100%; }
            .main-sidebar.show { left: 0; }
            .main-topbar { left: 0; padding: 0 20px; }
            .main-content { margin-left: 0; padding: 20px; }
            .omnibar { width: 40px; padding: 8px; justify-content: center; cursor: pointer; }
            .omnibar input { display: none; } /* Hide input on mobile initially */
            .omnibar:focus-within { width: 100%; position: absolute; left: 20px; right: 20px; z-index: 10; }
            .omnibar:focus-within input { display: block; }
        }
    </style>
</head>
<body>

    {{-- Overlay untuk mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="main-sidebar">
        
        <a href="{{ route('dashboard') }}" class="logo">ROMS</a>

        <div class="user-profile">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div> <div>
                <span class="username">{{ auth()->user()->name }}</span>
                <small class="d-block text-muted">{{ ucfirst(auth()->user()->role) }}</small>
            </div>
        </div>
        
        <ul class="nav flex-column">
            {{-- Dashboard & Overview --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door-fill"></i> Dashboard
                </a>
            </li>
            
            @if(auth()->user()->isCS())
            {{-- CS Dashboard --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('cs/dashboard') ? 'active' : '' }}" href="{{ route('cs.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Beranda CS
                </a>
            </li>
            @endif

            {{-- Divider --}}
            <li class="nav-item mt-2 mb-2">
                <hr class="sidebar-divider" style="border-color: rgba(255,255,255,0.2); margin: 0;">
            </li>
            <li class="nav-item">
                <small class="text-muted ps-3" style="font-size: 0.75rem; opacity: 0.7;">
                    {{ auth()->user()->isCS() ? 'LAYANAN' : 'DATA MASTER' }}
                </small>
            </li>

            {{-- Data Master / Service Menu --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('app/chat*') ? 'active' : '' }}" href="{{ route('chat.ui') }}">
                    <i class="bi bi-chat-dots-fill"></i> Chat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('cs/pelanggan*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                    <i class="bi bi-people-fill"></i> Pelanggan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('app/products*') ? 'active' : '' }}" href="{{ route('product.index') }}">
                    <i class="bi bi-box-seam-fill"></i> Produk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('app/orders*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                    <i class="bi bi-receipt-cutoff"></i> Pesanan
                </a>
            </li>

            @if(auth()->user()->isAdmin())
            {{-- Admin Only Features --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('cs/whatsapp/scan*') ? 'active' : '' }}" href="{{ route('whatsapp.scan') }}">
                    <i class="bi bi-whatsapp"></i> Koneksi WhatsApp
                </a>
            </li>

            {{-- Divider --}}
            <li class="nav-item mt-2 mb-2">
                <hr class="sidebar-divider" style="border-color: rgba(255,255,255,0.2); margin: 0;">
            </li>
            <li class="nav-item">
                <small class="text-muted ps-3" style="font-size: 0.75rem; opacity: 0.7;">AUTOMASI</small>
            </li>

            {{-- Automasi & Marketing --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('app/reminders*') ? 'active' : '' }}" href="{{ route('reminders.index') }}">
                    <i class="bi bi-bell-fill"></i> Reminder
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/otomasi-pesan*') ? 'active' : '' }}" href="{{ route('broadcast.index') }}">
                    <i class="bi bi-send-fill"></i> Broadcast
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/templates*') ? 'active' : '' }}" href="{{ route('admin.templates.index') }}">
                    <i class="bi bi-file-text-fill"></i> Template Pesan
                </a>
            </li>


            {{-- Divider --}}
            <li class="nav-item mt-2 mb-2">
                <hr class="sidebar-divider" style="border-color: rgba(255,255,255,0.2); margin: 0;">
            </li>
            <li class="nav-item">
                <small class="text-muted ps-3" style="font-size: 0.75rem; opacity: 0.7;">ADMIN</small>
            </li>

            {{-- Admin Management --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <i class="bi bi-bar-chart-fill"></i> Laporan Bisnis
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/api-integration*') ? 'active' : '' }}" href="{{ route('admin.api.index') }}">
                    <i class="bi bi-code-slash"></i> Integrasi API
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/cs*') ? 'active' : '' }}" href="{{ route('admin.cs.index') }}">
                    <i class="bi bi-people-fill"></i> Kelola CS
                </a>
            </li>
            @endif

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
            {{-- Left Side: Toggle & Page Title --}}
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-dark p-0 d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-bold text-dark">@yield('header-title', 'Dashboard')</h5>
                    <small class="text-muted">@yield('header-subtitle', 'Selamat datang kembali, ' . auth()->user()->name)</small>
                </div>
            </div>

            {{-- Right Side: Search, Status, Actions --}}
            <div class="d-flex align-items-center gap-3 ms-auto">
                
                {{-- Dynamic Actions (Buttons from pages) --}}
                @yield('topbar-actions')

                <!-- Navbar Search -->
                <form class="d-none d-lg-block position-relative">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input class="form-control bg-light border-start-0 rounded-end-pill ps-2" type="text" placeholder="Cari data..." aria-label="Search">
                    </div>
                </form>

                <!-- CS Status Toggle -->
                @if(auth()->check() && auth()->user()->role === 'cs')
                <form action="{{ route('cs.status.toggle') }}" method="POST" class="d-inline-block">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ auth()->user()->is_online ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3 fw-bold d-flex align-items-center gap-2">
                        <i class="bi {{ auth()->user()->is_online ? 'bi-circle-fill' : 'bi-moon-fill' }}" style="font-size: 0.6rem;"></i>
                        {{ auth()->user()->is_online ? 'ONLINE' : 'OFFLINE' }}
                    </button>
                </form>
                @endif

                <!-- Notifications -->
                <button class="btn btn-light position-relative rounded-circle p-2" style="width: 40px; height: 40px;">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </button>

                <!-- User Dropdown (Optional, for now just profile link) -->
                <div class="vr mx-2 d-none d-md-block"></div>
                
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            
            @yield('content')
            @yield('main-content')

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Sidebar Toggle Script untuk Mobile --}}
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
    
    {{-- Ini untuk style khusus per halaman --}}
    @stack('styles')
    
    {{-- Ini untuk script khusus per halaman --}}
    @stack('scripts')
</body>
</html>