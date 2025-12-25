@extends('layouts.app')

@section('title', 'Chat Dashboard')

@section('content')

{{-- FIX ERROR JS: Tambahkan Meta CSRF agar script global tidak error saat mencarinya --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Hidden Inputs untuk JS lokal --}}
<input type="hidden" id="csrf-token-input" value="{{ csrf_token() }}">
<input type="hidden" id="current-room-id">

{{-- 1. OVERLAY: JIKA WA BELUM TERHUBUNG (Dikontrol via JS) --}}
<div id="wa-disconnect-overlay" class="hidden absolute inset-0 z-50 bg-slate-50 flex flex-col items-center justify-center text-center h-full w-full">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md border border-slate-100">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="wifi-off" class="h-10 w-10 text-red-500"></i>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 mb-2">WhatsApp Terputus</h2>
        <p class="text-slate-500 mb-6">
            Sistem tidak dapat mendeteksi koneksi WhatsApp yang aktif. Silakan hubungkan ulang perangkat Anda untuk mengakses fitur chat.
        </p>
        {{-- Pastikan route ini ada di web.php --}}
        <a href="{{ route('whatsapp.scan') }}" class="inline-flex items-center justify-center px-6 py-3 bg-[#84994F] text-white font-bold rounded-xl hover:bg-[#6f8042] transition gap-2">
            <i data-lucide="qr-code" class="h-5 w-5"></i> Hubungkan WhatsApp
        </a>
    </div>
</div>

{{-- 2. MAIN CHAT INTERFACE (Container 3 Kolom) --}}
<div id="chat-app-wrapper" class="flex flex-1 h-full bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative">

    {{-- Loading State Awal --}}
    <div id="initial-loader" class="absolute inset-0 z-40 bg-white flex flex-col items-center justify-center">
        <div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-[#84994F] mb-3"></div>
        <p class="text-sm text-slate-500 font-medium">Memuat Data Chat...</p>
    </div>

    {{-- ==================== KOLOM 1: CHAT LIST (KIRI) ==================== --}}
    <div class="w-full sm:w-80 md:w-96 flex-none border-r border-slate-200 flex flex-col bg-white h-full z-20">
        
        {{-- Header List Chat --}}
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="font-bold text-slate-800 text-lg">Chat</h2>
            <button onclick="openNewChatModal()" class="flex items-center gap-1.5 px-3 py-1 bg-[#84994F] text-white rounded-lg text-xs font-semibold hover:bg-[#6a7c3f] transition">
                <i data-lucide="plus" class="h-4 w-4"></i> Chat Baru
            </button>
        </div>

        {{-- Search Bar --}}
        <div class="p-3 border-b border-slate-100">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                <input type="text" id="search-chat" placeholder="Cari percakapan..." class="w-full bg-slate-100 border border-slate-200 text-sm rounded-lg pl-9 pr-3 py-2 focus:ring-2 focus:ring-[#84994F] focus:outline-none">
            </div>
        </div>

        {{-- List Chat Scrollable --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-slate-100" id="chat-list-container">
            @forelse($rooms as $room)
                <div onclick="loadRoom({{ $room->id }}, this)" 
                     id="room-item-{{ $room->id }}"
                     class="room-item flex items-start gap-3 p-3 cursor-pointer hover:bg-indigo-50/50 transition-colors group relative"
                     data-id="{{ $room->id }}">
                     
                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg group-hover:bg-[#84994F] group-hover:text-white transition-colors">
                            {{ substr($room->customer->name ?? 'G', 0, 1) }}
                        </div>
                        @if($room->status == 'new')
                            <span class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 border-2 border-white rounded-full"></span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-1">
                            <h3 class="text-sm font-bold text-slate-800 truncate">{{ $room->customer->name ?? $room->customer->phone }}</h3>
                            <span class="text-[10px] text-slate-500 flex-shrink-0">
                                {{ $room->updated_at->format('H:i') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 truncate group-hover:text-slate-800 transition-colors">
                            {{-- Menampilkan pesan terakhir atau default --}}
                            {{ $room->latestMessage->message_content ?? 'Mulai percakapan baru' }}
                        </p>
                    </div>

                    {{-- Active Indicator Border --}}
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#84994F] hidden active-border"></div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400 flex flex-col items-center justify-center h-full">
                    <i data-lucide="message-square-off" class="h-12 w-12 mb-3 opacity-50"></i>
                    <p class="text-sm">Belum ada percakapan</p>
                </div>
            @endforelse
        </div>
    </div>


    {{-- ==================== KOLOM 2: CHAT WINDOW / LANDING ==================== --}}
    <div class="flex-1 flex flex-col bg-white overflow-hidden relative z-10">
        
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');"></div>

        {{-- STATE 1: LANDING PAGE (Default) --}}
        <div id="chat-placeholder" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-slate-50/90 text-center p-6">
            <div class="bg-white p-6 rounded-full shadow-lg mb-6 animate-bounce-slow">
                <i data-lucide="message-circle" class="h-16 w-16 text-[#84994F]"></i>
            </div>
            <h4 class="text-2xl font-bold text-slate-800 mb-2">Selamat Datang di ROMS Chat</h4>
            <p class="text-slate-500 max-w-sm">Pilih percakapan dari daftar sebelah kiri untuk mulai *chatting* dan melihat detail pelanggan.</p>
        </div>

        {{-- STATE 2: ACTIVE CHAT INTERFACE --}}
        <div id="chat-interface" class="flex flex-col h-full hidden z-30 bg-[#efe7dd]/30 relative">
            
            {{-- Chat Header --}}
            <div class="relative z-30 flex-none px-6 py-3 bg-white/95 backdrop-blur-sm border-b border-slate-200 flex justify-between items-center shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-full bg-[#84994F] flex items-center justify-center text-white font-bold text-lg shadow-sm" id="header-avatar">
                        -
                    </div>
                    <div>
                        <h6 class="text-base font-bold text-slate-800 leading-tight" id="header-name">Memuat...</h6>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            <p class="text-[10px] text-slate-500 font-medium" id="header-phone">Online</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-slate-400">
                    <button class="p-2 hover:bg-slate-100 rounded-full transition lg:hidden" onclick="toggleInfoPanel()" title="Info Customer">
                        <i data-lucide="info" class="h-5 w-5"></i>
                    </button>
                    <button class="p-2 hover:bg-slate-100 rounded-full transition" title="Cari"><i data-lucide="search" class="h-5 w-5"></i></button>
                </div>
            </div>

            {{-- Messages Area --}}
            <div class="relative z-10 flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar scroll-smooth" id="messages-box">
                {{-- Pesan akan di-inject via JS --}}
            </div>

            {{-- Chat Input --}}
            <div class="relative z-30 flex-none px-4 py-4 bg-white border-t border-slate-200 flex items-end gap-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <button class="p-2.5 text-slate-400 hover:bg-slate-100 rounded-full transition"><i data-lucide="smile" class="h-6 w-6"></i></button>
                <button class="p-2.5 text-slate-400 hover:bg-slate-100 rounded-full transition"><i data-lucide="paperclip" class="h-6 w-6"></i></button>
                
                <div class="flex-1 bg-slate-50 rounded-xl border border-slate-200 focus-within:border-[#84994F] focus-within:ring-1 focus-within:ring-[#84994F] transition overflow-hidden">
                    <textarea id="chat-input" rows="1" class="w-full px-4 py-3 text-sm text-slate-700 bg-transparent border-none focus:ring-0 placeholder:text-slate-400 resize-none max-h-32 custom-scrollbar" placeholder="Ketik pesan..."></textarea>
                </div>

                <button onclick="sendMessage()" id="btn-send" class="p-3 bg-[#84994F] text-white rounded-xl hover:bg-[#6a7c3f] shadow-lg shadow-green-100 transition transform active:scale-95 mb-0.5">
                    <i data-lucide="send" class="h-5 w-5 ml-0.5"></i>
                </button>
            </div>
        </div>

    </div>

    {{-- ==================== KOLOM 3: INFO CUSTOMER (KANAN) ==================== --}}
    <div id="customer-info-panel" class="hidden lg:flex w-80 flex-none bg-white border-l border-slate-200 flex-col h-full z-40 transition-all duration-300">
        
        {{-- State: Idle --}}
        <div id="info-idle" class="flex-1 flex flex-col items-center justify-center text-slate-300 p-6 text-center">
            <div class="bg-slate-50 p-4 rounded-full mb-3">
                <i data-lucide="user" class="h-10 w-10 opacity-50"></i>
            </div>
            <p class="text-sm font-medium text-slate-400">Info Customer</p>
        </div>

        {{-- State: Active --}}
        <div id="info-content" class="hidden flex-col h-full w-full">
            
            {{-- Profile Header --}}
            <div class="p-8 flex flex-col items-center border-b border-slate-100 bg-slate-50/30">
                <div class="h-24 w-24 rounded-full bg-slate-100 p-1 mb-4 shadow-sm">
                    <div id="info-avatar" class="w-full h-full rounded-full bg-[#84994F] flex items-center justify-center text-white font-bold text-4xl shadow-inner">
                        -
                    </div>
                </div>
                <h3 class="font-bold text-lg text-slate-800 text-center leading-tight mb-1" id="info-name">-</h3>
                <p class="text-xs font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded mb-4 border border-slate-200" id="info-phone">-</p>
                
                <div class="grid grid-cols-2 gap-2 w-full">
                    <button class="py-2 text-xs font-bold text-[#84994F] border border-[#84994F] rounded-lg hover:bg-[#84994F] hover:text-white transition shadow-sm">
                        Lihat CRM
                    </button>
                    <button onclick="openAddCustomerModal()" class="py-2 text-xs font-bold text-slate-600 border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">
                        Edit
                    </button>
                </div>
            </div>

            {{-- Info Details --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <div class="p-6 border-b border-slate-50">
                    <h4 class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-4">Detail Kontak</h4>
                    
                    <div class="space-y-4">
                        <div class="flex gap-3 group">
                            <div class="mt-0.5 text-slate-300 group-hover:text-[#84994F] transition"><i data-lucide="map-pin" class="h-4 w-4"></i></div>
                            <div>
                                <p class="text-[10px] uppercase text-slate-400 font-bold mb-0.5">Alamat</p>
                                <p id="info-address" class="text-xs text-slate-600 leading-relaxed">-</p>
                            </div>
                        </div>
                        <div class="flex gap-3 group">
                            <div class="mt-0.5 text-slate-300 group-hover:text-[#84994F] transition"><i data-lucide="mail" class="h-4 w-4"></i></div>
                            <div>
                                <p class="text-[10px] uppercase text-slate-400 font-bold mb-0.5">Email</p>
                                <p id="info-email" class="text-xs text-slate-600 leading-relaxed">-</p>
                            </div>
                        </div>
                        <div class="flex gap-3 group">
                            <div class="mt-0.5 text-slate-300 group-hover:text-[#84994F] transition"><i data-lucide="tag" class="h-4 w-4"></i></div>
                            <div>
                                <p class="text-[10px] uppercase text-slate-400 font-bold mb-0.5">Label</p>
                                <span id="info-tag" class="inline-block bg-yellow-50 text-yellow-700 border border-yellow-100 text-[10px] px-2 py-0.5 rounded font-bold">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Orders --}}
                <div class="p-6 bg-slate-50/30 min-h-full">
                    <h4 class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-4 flex justify-between items-center">
                        Pesanan Terakhir
                    </h4>
                    
                    <div id="order-list" class="space-y-3">
                        {{-- Orders injected --}}
                        <p class="text-xs text-center text-slate-400 italic py-2">Memuat data pesanan...</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Modal New Chat --}}
<div class="fixed inset-0 z-[60] hidden" id="newChatModal" aria-hidden="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal('newChatModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-bold text-slate-800">Mulai Chat Baru</h5>
            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="closeModal('newChatModal')"><i data-lucide="x" class="h-5 w-5"></i></button>
        </div>
        <form id="newChatForm" onsubmit="handleNewChat(event)">
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-1">Nomor WhatsApp</label>
                <input type="text" class="w-full p-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-[#84994F] outline-none" id="newChatPhone" placeholder="Contoh: 08123456789" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Pelanggan (Opsional)</label>
                <input type="text" class="w-full p-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-[#84994F] outline-none" id="newChatName" placeholder="Nama Pelanggan">
            </div>
            <button type="submit" class="w-full py-2.5 bg-[#84994F] text-white font-bold rounded-lg hover:bg-[#6a7c3f] transition" id="btnStartChat">
                Mulai Chat
            </button>
        </form>
    </div>
</div>

{{-- MODAL ADD CUSTOMER --}}
<div class="fixed inset-0 z-50 hidden" id="addCustomerModal" aria-hidden="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal('addCustomerModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-bold text-slate-800">Edit Pelanggan</h5>
            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="closeModal('addCustomerModal')"><i data-lucide="x" class="h-5 w-5"></i></button>
        </div>
        <form id="addCustomerForm" onsubmit="handleSaveCustomer(event)">
            <input type="hidden" id="editCustomerId">
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-1">Nomor WhatsApp</label>
                <input type="text" class="w-full p-2 border border-slate-200 bg-slate-50 rounded-lg text-sm text-slate-500" id="editCustomerPhone" readonly disabled>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Pelanggan</label>
                <input type="text" class="w-full p-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-[#84994F] outline-none" id="editCustomerName" placeholder="Masukkan nama pelanggan" required>
            </div>
            <button type="submit" class="w-full py-2.5 bg-[#84994F] text-white font-bold rounded-lg hover:bg-[#6a7c3f] transition" id="btnSaveCustomer">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // --- CONFIGURASI API ---
    const API = {
        status: "{{ route('admin.whatsapp.api.status') }}",
        roomData: "/app/chat/room/:id/data",
        sendMsg: "/app/chat/room/:id/send-ajax"
    };
    
    // FIX CSRF: Gunakan input hidden jika meta tidak tersedia
    const csrfElement = document.getElementById('csrf-token-input');
    const CSRF = csrfElement ? csrfElement.value : '';

    let activeRoomId = null;

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
        
        // Cek Koneksi WA Saat Halaman Dimuat
        checkWAConnection();

        // Auto Resize Textarea
        const chatInput = document.getElementById('chat-input');
        if(chatInput) {
            chatInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            chatInput.addEventListener('keydown', function(e) {
                if(e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }
    });

    // --- FUNGSI CEK KONEKSI WA ---
    async function checkWAConnection() {
        try {
            const res = await fetch(API.status);
            const data = await res.json();
            
            // Hilangkan loader awal setelah cek selesai
            document.getElementById('initial-loader').classList.add('hidden');

            // Logika: Jika status CONNECTED atau READY, izinkan akses.
            const status = data.account ? data.account.status : (data.status || 'DISCONNECTED');

            if (status === 'CONNECTED' || status === 'READY') {
                document.getElementById('wa-disconnect-overlay').classList.add('hidden');
            } else {
                // Jika terputus, tampilkan overlay blokir
                document.getElementById('wa-disconnect-overlay').classList.remove('hidden');
            }
        } catch (e) {
            console.error("Gagal cek koneksi WA:", e);
            // Jangan blokir jika API status error (misal network), cukup hilangkan loader
            document.getElementById('initial-loader').classList.add('hidden');
        }
    }

    // --- FUNGSI LOAD ROOM (AJAX) ---
    async function loadRoom(roomId, element) {
        activeRoomId = roomId;
        document.getElementById('current-room-id').value = roomId;

        // Highlight Item di Sidebar
        document.querySelectorAll('.room-item').forEach(el => {
            el.classList.remove('bg-indigo-50', 'border-l-4', 'border-indigo-600');
            el.querySelector('.active-border').classList.add('hidden');
        });
        if(element) {
            element.classList.add('bg-indigo-50');
            element.querySelector('.active-border').classList.remove('hidden');
        }

        // Switch Tampilan ke Chat Aktif
        document.getElementById('chat-placeholder').classList.add('hidden');
        document.getElementById('chat-interface').classList.remove('hidden');
        document.getElementById('chat-interface').classList.add('flex');
        
        document.getElementById('info-idle').classList.add('hidden');
        document.getElementById('info-content').classList.remove('hidden');
        document.getElementById('info-content').classList.add('flex');

        // Reset Pesan ke Loading
        document.getElementById('messages-box').innerHTML = '<div class="flex justify-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#84994F]"></div></div>';

        try {
            const url = API.roomData.replace(':id', roomId);
            const res = await fetch(url);
            const data = await res.json();

            // Render Data
            renderChat(data);
            renderInfo(data.room.customer);
            renderOrders(data.orders || []);
            
        } catch (e) {
            console.error(e);
            alert('Gagal memuat data chat.');
        }
    }

    function renderChat(data) {
        const customer = data.room.customer;
        
        // Update Header Chat
        document.getElementById('header-name').innerText = customer.name;
        document.getElementById('header-avatar').innerText = customer.name.charAt(0).toUpperCase();
        document.getElementById('header-phone').innerText = customer.phone; // Atau status online

        // Update Messages
        const container = document.getElementById('messages-box');
        container.innerHTML = ''; // Hapus loading

        if(data.messages.length === 0) {
            container.innerHTML = '<div class="text-center text-slate-400 text-sm py-10">Belum ada pesan.</div>';
            return;
        }

        data.messages.forEach(msg => {
            const isMe = msg.sender_type === 'admin' || msg.sender_type === 'cs';
            const align = isMe ? 'justify-end' : 'justify-start';
            const bg = isMe ? 'bg-[#d9fdd3] text-slate-800' : 'bg-white text-slate-800';
            const radius = isMe ? 'rounded-tr-none' : 'rounded-tl-none';
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
            
            const checkIcon = isMe ? '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500"><path d="M20 6 9 17l-5-5"/></svg>' : '';

            const html = `
                <div class="flex ${align} group animate-fade-in">
                    <div class="relative max-w-[80%] px-3 py-2 rounded-lg shadow-sm text-sm leading-relaxed ${bg} ${radius} border border-slate-100">
                        <span>${msg.message_content}</span>
                        <div class="flex items-center justify-end gap-1 mt-1 select-none float-right ml-2 relative top-0.5">
                            <span class="text-[10px] text-slate-500">${time}</span>
                            ${checkIcon}
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });

        // Scroll ke bawah
        container.scrollTop = container.scrollHeight;
    }

    function renderInfo(customer) {
        document.getElementById('info-name').innerText = customer.name;
        document.getElementById('info-phone').innerText = customer.phone;
        document.getElementById('info-avatar').innerText = customer.name.charAt(0).toUpperCase();
        
        document.getElementById('info-address').innerText = customer.address || '-';
        document.getElementById('info-email').innerText = customer.email || '-';
        document.getElementById('info-tag').innerText = customer.tag || 'New';
    }

    function renderOrders(orders) {
        const list = document.getElementById('order-list');
        list.innerHTML = '';

        if (orders.length === 0) {
            list.innerHTML = '<p class="text-xs text-center text-slate-400 italic py-4">Tidak ada riwayat pesanan.</p>';
            return;
        }

        orders.forEach(order => {
            const statusColor = order.status === 'completed' ? 'text-green-600 bg-green-50' : 'text-amber-600 bg-amber-50';
            const html = `
                <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm hover:border-[#84994F] transition cursor-pointer">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-700">${order.order_number}</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase ${statusColor}">${order.status}</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-slate-500">
                        <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> ${order.date}</span>
                        <span class="font-bold text-slate-700">Rp ${order.total_amount}</span>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', html);
        });
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // --- FUNGSI KIRIM PESAN ---
    async function sendMessage() {
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        
        if(!text || !activeRoomId) return;

        const container = document.getElementById('messages-box');
        const time = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
        
        container.insertAdjacentHTML('beforeend', `
            <div class="flex justify-end group animate-fade-in opacity-50">
                <div class="relative max-w-[80%] px-3 py-2 rounded-lg shadow-sm text-sm leading-relaxed bg-[#d9fdd3] rounded-tr-none text-slate-800 border border-slate-100">
                    <span>${text}</span>
                    <div class="flex items-center justify-end gap-1 mt-1 select-none float-right ml-2">
                        <span class="text-[10px] text-slate-500">${time}</span>
                        <i data-lucide="clock" class="w-3 h-3 text-slate-400"></i>
                    </div>
                </div>
            </div>
        `);
        container.scrollTop = container.scrollHeight;
        input.value = '';
        if(typeof lucide !== 'undefined') lucide.createIcons();

        try {
            const url = API.sendMsg.replace(':id', activeRoomId);
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({ message_body: text })
            });
            const json = await res.json();
            
            if(json.success) {
                loadRoom(activeRoomId); 
            } else {
                alert('Gagal kirim: ' + (json.error || 'Unknown error'));
            }
        } catch(e) {
            console.error(e);
            alert('Terjadi kesalahan jaringan.');
        }
    }

    // --- MODAL HELPERS ---
    function openNewChatModal() {
        document.getElementById('newChatModal').classList.remove('hidden');
    }
    function openAddCustomerModal() {
        document.getElementById('addCustomerModal').classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
    
    function toggleInfoPanel() {
        const panel = document.getElementById('customer-info-panel');
        panel.classList.toggle('hidden');
        panel.classList.toggle('flex');
        panel.classList.toggle('absolute');
        panel.classList.toggle('right-0');
        panel.classList.toggle('h-full');
        panel.style.zIndex = 50;
    }
</script>
@endpush
