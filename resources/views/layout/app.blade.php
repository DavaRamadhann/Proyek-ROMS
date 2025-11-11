<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ROMS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">ROMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    
                    {{-- =============================================== --}}
                    {{-- ============ TAMBAHAN BARU DI SINI ============ --}}
                    {{-- =============================================== --}}

                    @auth
                        {{-- @auth hanya akan menjalankan kode di dalamnya JIKA user sudah login --}}
                        {{-- Dengan begitu, auth()->user() tidak akan pernah null di sini --}}
                        @if(in_array(auth()->user()->role, ['admin', 'cs']))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('chat.index') }}">
                                    💬 Chat Inbox
                                </a>
                            </li>
                        @endif
                    @endauth
                    
                    {{-- (Nanti link ke Customer, Order, dll. bisa ditambah di sini) --}}
                    
                    {{-- =============================================== --}}
                    {{-- ============ AKHIR TAMBAHAN BARU ============ --}}
                    {{-- =============================================== --}}

                </ul>
                
                {{-- Ini adalah form logout yang ada di dashboard --}}
                {{-- Kita pindahkan ke navbar agar lebih rapi --}}
                @auth
                <form method="POST" action="{{ route('logout') }}" class="d-flex">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>