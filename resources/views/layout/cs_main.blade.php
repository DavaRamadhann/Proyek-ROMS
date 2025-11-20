<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- PENTING: CSRF Token untuk AJAX Chat --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'CS Dashboard')</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Bootstrap Icons (PENTING: Agar icon chat muncul) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    {{-- Font Google (Opsional, agar terlihat modern) --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
        
        /* Navbar Sederhana CS */
        .cs-navbar { background: white; border-bottom: 1px solid #e9edef; height: 60px; display: flex; align-items: center; padding: 0 20px; }
        .cs-brand { font-weight: 800; color: #B45253; font-size: 1.2rem; text-decoration: none; margin-right: 30px; }
        .nav-link { color: #555; text-decoration: none; margin-right: 20px; font-weight: 500; }
        .nav-link:hover, .nav-link.active { color: #B45253; }
    </style>
    @stack('styles')
</head>
<body>

    {{-- Navbar Global --}}
    <nav class="cs-navbar">
        <a href="#" class="cs-brand">ROMS <span class="text-dark fw-light">CS</span></a>
        <a href="{{ route('chat.dashboard') }}" class="nav-link">Dashboard</a>
        <a href="{{ route('chat.whatsapp') }}" class="nav-link active">Chat</a>
        <a href="#" class="nav-link">Pelanggan</a>
        <a href="#" class="nav-link">Pesanan</a>
    </nav>

    {{-- Konten Utama --}}
    <div class="main-content">
        @yield('main-content')
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>