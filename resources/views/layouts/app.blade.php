<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>ROMS - @yield('title', 'Dashboard')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Scrollbar Halus */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        .sidebar-transition { transition: width 0.3s ease, transform 0.3s ease; }
    </style>
</head>
<body class="bg-[#f1f5f9] text-slate-800 h-screen w-full overflow-hidden flex flex-col md:flex-row">

    {{-- MOBILE TOP BAR --}}
    <div class="md:hidden flex-none h-16 bg-[#84994F] text-white flex items-center justify-between px-4 shadow-md z-30">
        <div class="flex items-center gap-3">
            <button onclick="toggleMobileSidebar()" class="p-1 rounded hover:bg-white/10"><i data-lucide="menu" class="h-6 w-6"></i></button>
            <span class="font-bold text-lg tracking-tight">ROMS App</span>
        </div>
        <div class="h-8 w-8 rounded-full bg-[#FCB53B] flex items-center justify-center text-[#B45253] font-bold text-xs border border-white/20">
            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
        </div>
    </div>

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="sidebar-transition fixed inset-y-0 left-0 bg-[#84994F] text-white flex-col h-full shadow-xl z-50 transform -translate-x-full md:translate-x-0 md:static md:flex w-64 whitespace-nowrap overflow-hidden">
        <button onclick="toggleMobileSidebar()" class="absolute top-4 right-4 md:hidden text-white/70 hover:text-white"><i data-lucide="x" class="h-6 w-6"></i></button>
        
        {{-- HEADER SIDEBAR --}}
        <div class="h-16 flex items-center px-6 border-b border-white/10 shrink-0 min-w-[256px]">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 bg-white rounded-lg flex items-center justify-center text-[#84994F] shadow-sm">
                    <i data-lucide="package-2" class="h-5 w-5"></i>
                </div>
                <div><h1 class="text-lg font-bold tracking-tight">ROMS</h1></div>
            </div>
        </div>

        {{-- MENU NAVIGASI --}}
        <nav class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-1 min-w-[256px]">
            
            {{-- BAGIAN 1: UTAMA --}}
            <div class="px-3 mb-2 mt-2 text-[10px] font-bold uppercase tracking-wider text-white/60">Utama</div>
            
            <a href="{{ Auth::user()->role === 'cs' ? route('cs.dashboard') : route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('dashboard') || request()->routeIs('cs.dashboard') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="layout-dashboard" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Dashboard</span>
            </a>

            <a href="{{ route('chat.ui') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('chat.*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="message-square-text" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Chat</span>
            </a>

            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('customers*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="users" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Pelanggan</span>
            </a>

            <a href="{{ route('product.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('product*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="package" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Produk</span>
            </a>

            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('orders*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="shopping-cart" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Pesanan</span>
            </a>
            
            {{-- BAGIAN 2: OTOMASI --}}
            @if(Auth::user()->role === 'admin')
            <div class="px-3 mb-2 mt-6 text-[10px] font-bold uppercase tracking-wider text-white/60">Otomasi</div>
            
            <a href="{{ route('reminders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('reminders*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="bell-ring" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Reminder</span>
            </a>

            <a href="{{ route('broadcast.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('broadcast*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="megaphone" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Broadcast</span>
            </a>

            <a href="{{ route('admin.templates.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('admin.templates*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="file-text" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Template Pesan</span>
            </a>
            @endif

            {{-- BAGIAN 3: ADMIN --}}
            @if(Auth::user()->role === 'admin')
            <div class="px-3 mb-2 mt-6 text-[10px] font-bold uppercase tracking-wider text-white/60">Pengaturan</div>
            
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('admin.reports*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="bar-chart-3" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Laporan Bisnis</span>
            </a>

            <a href="{{ route('admin.cs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('admin.cs*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="shield" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Kelola CS</span>
            </a>

            <a href="{{ route('whatsapp.scan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('whatsapp.scan') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="qr-code" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Koneksi WhatsApp</span>
            </a>

            <a href="{{ route('admin.api.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group {{ request()->routeIs('admin.api*') ? 'bg-[#FFE797] text-[#B45253] shadow-sm font-bold' : 'text-white/90 hover:bg-white/10' }}">
                <i data-lucide="webhook" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm">Konfigurasi API</span>
            </a>
            @endif

            {{-- MENU LOGOUT DI SIDEBAR --}}
            <div class="mt-6 pt-6 border-t border-white/10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-white/90 hover:bg-red-500/20 hover:text-red-100 rounded-lg transition-all group">
                        <i data-lucide="log-out" class="h-5 w-5 flex-shrink-0"></i><span class="text-sm font-medium">Logout</span>
                    </button>
                </form>
            </div>

        </nav>
    </aside>

    <div id="sidebarOverlay" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden"></div>

    {{-- CONTENT WRAPPER --}}
    <main class="flex-1 flex flex-col h-full overflow-hidden relative w-full transition-all bg-[#f1f5f9]">
        
        {{-- TOP BAR --}}
        <header class="h-16 bg-white border-b border-slate-200 shadow-sm flex items-center justify-between px-4 z-30 shrink-0 relative">
            
            {{-- KIRI: Toggle & Search --}}
            <div class="flex items-center gap-4 flex-1 mr-4 lg:mr-8"> 
                <button onclick="toggleDesktopSidebar()" class="hidden md:flex p-2 rounded-lg text-slate-400 hover:text-[#84994F] hover:bg-slate-50 transition-colors flex-shrink-0">
                    <i data-lucide="panel-left" class="h-5 w-5" id="toggleIcon"></i>
                </button>

                {{-- SEARCH BAR --}}
                <div class="hidden md:flex relative w-full"> 
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                    <input type="text" placeholder="Cari pesanan, pelanggan, atau produk..." class="w-full bg-slate-100 border-none text-sm rounded-full pl-9 pr-4 py-2 focus:ring-1 focus:ring-[#84994F] focus:outline-none transition-all">
                </div>
            </div>

            {{-- KANAN: NOTIFIKASI & PROFIL --}}
            <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
                
                {{-- Notifikasi --}}
                <div class="relative">
                    <button id="notifButton" onclick="toggleDropdown('notif')" class="relative p-2 text-slate-400 hover:text-[#84994F] transition-colors focus:outline-none">
                        <i data-lucide="bell" class="h-5 w-5"></i>
                        {{-- Badge Notifikasi --}}
                        <span id="notifBadge" class="hidden absolute top-1 right-1 h-4 w-4 bg-[#B45253] text-white text-[10px] font-bold rounded-full flex items-center justify-center border border-white">0</span>
                    </button>

                    <div id="notifDropdown" class="hidden absolute top-12 right-0 w-80 bg-white rounded-xl shadow-xl border border-slate-100 z-50 overflow-hidden transform transition-all origin-top-right">
                        <div class="px-4 py-3 border-b border-slate-50 bg-slate-50 flex justify-between items-center">
                            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Notifikasi</h3>
                        </div>
                        <div id="notifList" class="max-h-64 overflow-y-auto custom-scrollbar">
                            <div class="p-4 text-center text-xs text-slate-500">
                                Tidak ada notifikasi baru.
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // Polling Notifikasi
                    function pollNotifications() {
                        fetch('/chat/notifications')
                            .then(response => response.json())
                            .then(data => {
                                updateNotificationUI(data);
                            })
                            .catch(err => console.error('Error fetching notifications:', err));
                    }

                    function updateNotificationUI(data) {
                        const badge = document.getElementById('notifBadge');
                        const list = document.getElementById('notifList');
                        
                        // Update Badge
                        if (data.count > 0) {
                            badge.innerText = data.count > 99 ? '99+' : data.count;
                            badge.classList.remove('hidden');
                            badge.classList.add('flex');
                        } else {
                            badge.classList.add('hidden');
                            badge.classList.remove('flex');
                        }

                        // Update List
                        if (data.messages && data.messages.length > 0) {
                            let html = '';
                            data.messages.forEach(msg => {
                                html += `
                                    <a href="/app/chat/ui?room=${msg.room_id}" class="block px-4 py-3 hover:bg-slate-50 transition border-b border-slate-50 last:border-0">
                                        <div class="flex gap-3">
                                            <div class="h-8 w-8 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                ${msg.initial}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex justify-between items-baseline mb-1">
                                                    <p class="text-xs font-bold text-slate-800 truncate">${msg.sender_name}</p>
                                                    <span class="text-[10px] text-slate-400 flex-shrink-0">${msg.time}</span>
                                                </div>
                                                <p class="text-xs text-slate-500 truncate">${msg.message}</p>
                                            </div>
                                        </div>
                                    </a>
                                `;
                            });
                            list.innerHTML = html;
                        } else {
                            list.innerHTML = `
                                <div class="p-4 text-center text-xs text-slate-500">
                                    Tidak ada notifikasi baru.
                                </div>
                            `;
                        }
                    }

                    // Start Polling setiap 3 detik (lebih cepat)
                    setInterval(pollNotifications, 3000);
                    
                    // Initial Load
                    document.addEventListener('DOMContentLoaded', pollNotifications);
                </script>
                
                <div class="h-6 w-px bg-slate-200"></div>

                {{-- Profil --}}
                <div class="relative">
                    <button id="profileButton" onclick="toggleDropdown('profile')" class="flex items-center gap-2 cursor-pointer focus:outline-none group">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-bold text-slate-700 group-hover:text-[#84994F] transition-colors">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-slate-500 uppercase">{{ Auth::user()->role }}</div>
                        </div>
                        <div class="h-8 w-8 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold text-xs shadow-sm group-hover:shadow-md transition-all border-2 border-[#f1f5f9] group-hover:border-[#84994F]">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </button>

                    <div id="profileDropdown" class="hidden absolute top-12 right-0 w-48 bg-white rounded-xl shadow-xl border border-slate-100 z-50 overflow-hidden transform transition-all origin-top-right">
                        <div class="px-4 py-3 border-b border-slate-50">
                            <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="border-t border-slate-50 py-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-xs text-[#B45253] hover:bg-red-50 transition-colors text-left">
                                    <i data-lucide="log-out" class="h-3.5 w-3.5"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <div class="flex-1 flex flex-col h-full overflow-hidden p-6 md:p-8">
            <div class="flex-1 overflow-y-auto custom-scrollbar flex flex-col gap-6 pr-2">
                {{-- FLASH MESSAGES --}}
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2 mb-4" role="alert">
                    <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 mb-4" role="alert">
                    <i data-lucide="alert-circle" class="h-5 w-5"></i>
                    <span class="block sm:inline font-medium">{{ session('error') }}</span>
                </div>
                @endif
                
                {{-- ISI KONTEN MUNCUL DI SINI --}}
                @yield('content')
            </div>
        </div>
    </main>

    <script>
        // lucide.createIcons(); // Removed: handled by app.js

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); } else { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }
        }
        function toggleDesktopSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('w-64')) { sidebar.classList.remove('w-64'); sidebar.classList.add('w-0'); sidebar.classList.remove('border-r'); } else { sidebar.classList.remove('w-0'); sidebar.classList.add('w-64'); }
        }
        function toggleDropdown(type) {
            const notif = document.getElementById('notifDropdown');
            const profile = document.getElementById('profileDropdown');
            if (type === 'notif') { notif.classList.toggle('hidden'); profile.classList.add('hidden'); }
            else if (type === 'profile') { profile.classList.toggle('hidden'); notif.classList.add('hidden'); }
        }
        document.addEventListener('click', function(event) {
            const notifDrop = document.getElementById('notifDropdown'); const notifBtn = document.getElementById('notifButton');
            const profDrop = document.getElementById('profileDropdown'); const profBtn = document.getElementById('profileButton');
            if (!notifBtn.contains(event.target) && !notifDrop.contains(event.target)) { notifDrop.classList.add('hidden'); }
            if (!profBtn.contains(event.target) && !profDrop.contains(event.target)) { profDrop.classList.add('hidden'); }
        });
    </script>
    @stack('scripts')
</body>
</html>