@extends('layout.cs_main')

@section('title', 'Obrolan - ROMS')

@push('styles')
<style>
    /* --- LAYOUT UTAMA --- */
    .main-content {
        padding: 0 !important;
        height: calc(100vh - 70px); /* Sesuaikan tinggi navbar */
        display: flex;
        flex-direction: column;
        overflow: hidden;
        position: relative;
    }

    /* --- BLOKIR LAYAR (QR / KONEKSI) --- */
    #connection-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: #f0f2f5;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .qr-container {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    /* --- CONTAINER CHAT 3 KOLOM --- */
    .chat-layout {
        display: flex;
        width: 100%;
        height: 100%;
        background: #fff;
    }

    /* KOLOM 1: LIST CHAT */
    .col-chat-list {
        width: 350px;
        border-right: 1px solid #e9edef;
        display: flex;
        flex-direction: column;
        background: #fff;
        flex-shrink: 0;
    }
    .chat-search-box {
        padding: 15px;
        background: #f0f2f5;
        border-bottom: 1px solid #e9edef;
    }
    .chat-list-scroll {
        flex: 1;
        overflow-y: auto;
    }
    .chat-item {
        display: flex;
        padding: 15px;
        cursor: pointer;
        border-bottom: 1px solid #f5f6f6;
        transition: 0.2s;
    }
    .chat-item:hover { background: #f5f6f6; }
    .chat-item.active { background: #f0f2f5; border-left: 4px solid #B45253; }
    
    .avatar-circle {
        width: 45px; height: 45px;
        border-radius: 50%;
        background: #ddd;
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold;
        margin-right: 15px;
        flex-shrink: 0;
    }

    /* KOLOM 2: AREA CHAT (TENGAH) */
    .col-chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #efe7dd; /* Warna Background WA Default */
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        position: relative;
        min-width: 0; /* Prevent flex overflow */
    }
    .chat-header {
        height: 60px;
        background: #f0f2f5;
        padding: 0 20px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #d1d7db;
        justify-content: space-between;
    }
    .messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 20px 5%;
        display: flex;
        flex-direction: column;
    }
    .chat-input-area {
        background: #f0f2f5;
        padding: 10px 20px;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    /* Bubbles */
    .msg-row { display: flex; margin-bottom: 10px; }
    .msg-row.outgoing { justify-content: flex-end; }
    .msg-row.incoming { justify-content: flex-start; }
    
    .msg-bubble {
        max-width: 65%;
        padding: 8px 12px;
        border-radius: 8px;
        position: relative;
        font-size: 14px;
        line-height: 1.4;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .msg-bubble.in { background: #fff; border-top-left-radius: 0; }
    .msg-bubble.out { background: #d9fdd3; border-top-right-radius: 0; }
    
    .msg-time {
        font-size: 10px; color: #999;
        text-align: right; margin-top: 4px;
    }

    /* KOLOM 3: PROFIL (KANAN) */
    .col-chat-profile {
        width: 300px;
        border-left: 1px solid #e9edef;
        background: #fff;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }
    .profile-header {
        padding: 30px 20px;
        text-align: center;
        background: #f0f2f5;
        border-bottom: 1px solid #ddd;
    }
    .profile-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }
    
    /* Utility */
    .d-none { display: none !important; }
    .btn-maroon { background-color: #B45253; color: white; border: none; }
    .btn-maroon:hover { background-color: #9a4243; color: white; }
</style>
@endpush

@section('main-content')

{{-- 1. OVERLAY KONEKSI / QR --}}
<div id="connection-overlay">
    <div class="qr-container d-none" id="qr-wrapper">
        <div id="qrcode"></div>
        <p class="mt-3 fw-bold text-muted">Scan QR via WhatsApp di HP Anda</p>
    </div>

    <div id="loading-wrapper" class="text-center">
        <div class="spinner-border text-maroon mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
        <h4 class="text-muted" id="status-text">Menghubungkan ke WhatsApp...</h4>
        <button id="btn-reconnect" class="btn btn-maroon mt-3 d-none" onclick="triggerReconnect()">Coba Hubungkan Ulang</button>
    </div>
</div>

{{-- 2. INTERFACE CHAT UTAMA --}}
<div class="chat-layout d-none" id="chat-interface">
    
    {{-- KOLOM KIRI: DAFTAR CHAT --}}
    <aside class="col-chat-list">
        <div class="chat-search-box">
            <input type="text" class="form-control" placeholder="Cari atau mulai chat baru">
        </div>
        <div class="chat-list-scroll">
            @forelse($rooms as $room)
            <div class="chat-item" onclick="openChat({{ $room->id }}, this)" data-id="{{ $room->id }}">
                <div class="avatar-circle" style="background-color: #B45253;">
                    {{ substr($room->customer->name ?? 'G', 0, 1) }}
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold text-dark">{{ $room->customer->name ?? 'Guest' }}</span>
                        <span class="small text-muted">{{ $room->updated_at->format('H:i') }}</span>
                    </div>
                    <div class="small text-muted text-truncate">
                        {{-- Tampilkan pesan terakhir jika ada --}}
                        @if($room->messages->isNotEmpty())
                            {{ $room->messages->last()->message_content }}
                        @else
                            Belum ada pesan
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">Belum ada riwayat chat</div>
            @endforelse
        </div>
    </aside>

    {{-- KOLOM TENGAH: AREA CHAT --}}
    <main class="col-chat-main">
        {{-- Placeholder saat belum pilih chat --}}
        <div id="no-chat-selected" class="d-flex flex-column align-items-center justify-content-center h-100 bg-white">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" width="80" class="opacity-25 mb-3">
            <h5 class="text-muted">Pilih percakapan untuk mulai ngobrol</h5>
        </div>

        {{-- Wrapper Chat Aktif --}}
        <div id="active-chat-wrapper" class="d-none flex-column h-100">
            <header class="chat-header">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle me-2" id="header-avatar" style="width:40px;height:40px;background:#B45253;">A</div>
                    <div>
                        <div class="fw-bold" id="header-name">Nama Pelanggan</div>
                        <div class="small text-muted" id="header-status">Online</div>
                    </div>
                </div>
                <button class="btn btn-sm btn-light" onclick="toggleProfile()"><i class="bi bi-info-circle"></i> Info</button>
            </header>

            <div class="messages-area" id="messages-box">
                {{-- Pesan akan dimuat via JS --}}
            </div>

            <footer class="chat-input-area">
                <input type="hidden" id="active-room-id">
                <button class="btn btn-light text-muted"><i class="bi bi-emoji-smile"></i></button>
                <button class="btn btn-light text-muted"><i class="bi bi-paperclip"></i></button>
                <input type="text" id="message-input" class="form-control" placeholder="Ketik pesan..." onkeypress="handleEnter(event)">
                <button class="btn btn-maroon" onclick="sendMessage()"><i class="bi bi-send-fill"></i></button>
            </footer>
        </div>
    </main>

    {{-- KOLOM KANAN: PROFIL --}}
    <aside class="col-chat-profile" id="profile-panel">
        <div class="profile-header">
            <div class="avatar-circle mx-auto mb-3" id="profile-avatar" style="width:80px;height:80px;font-size:2rem;background:#B45253;">A</div>
            <h5 class="fw-bold mb-0" id="profile-name">Nama Pelanggan</h5>
            <small class="text-muted" id="profile-phone">+62 812-3456-7890</small>
        </div>
        <div class="profile-body">
            <h6 class="fw-bold text-maroon mb-3">Detail Informasi</h6>
            <div class="mb-3">
                <label class="small text-muted d-block">Email</label>
                <span class="fw-bold" id="profile-email">-</span>
            </div>
            <div class="mb-3">
                <label class="small text-muted d-block">Alamat</label>
                <span id="profile-address">-</span>
            </div>
            
            <h6 class="fw-bold text-maroon mt-4 mb-3">Riwayat Pesanan</h6>
            <div id="order-history-list">
                <div class="text-muted small fst-italic">Data dummy belum terhubung API pesanan.</div>
            </div>
        </div>
    </aside>

</div>

<script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
<script>
    const URLS = {
        status: "{{ route('admin.whatsapp.api.status') }}",
        qr: "{{ route('admin.whatsapp.api.qr') }}",
        reconnect: "{{ route('admin.whatsapp.api.reconnect') }}",
        getChat: "{{ url('/chat/room/:id/data') }}",
        sendChat: "{{ url('/chat/room/:id/send-ajax') }}"
    };
    const CSRF_TOKEN = "{{ csrf_token() }}";

    let activeRoomId = null;
    let pollingInterval = null;

    // 1. CEK KONEKSI SAAT LOAD
    document.addEventListener('DOMContentLoaded', () => {
        checkWAConnection();
        setInterval(checkWAConnection, 5000); // Cek status tiap 5 detik
    });

    async function checkWAConnection() {
        try {
            // Jika sedang aktif chat, skip cek status untuk mengurangi flicker, 
            // kecuali kalau mau strict real-time error handling.
            
            const res = await fetch(URLS.status);
            const data = await res.json();

            if (data.status === 'READY' || data.status === 'AUTHENTICATED') {
                // SUKSES: Tampilkan Chat
                document.getElementById('connection-overlay').classList.add('d-none');
                document.getElementById('chat-interface').classList.remove('d-none');
            } else if (data.status === 'QR') {
                // Tampilkan QR
                showOverlay('QR');
                fetchQR();
            } else {
                // Disconnected / Lainnya
                showOverlay('DISCONNECTED');
            }
        } catch (e) {
            console.error("Gagal cek status WA", e);
        }
    }

    function showOverlay(type) {
        const overlay = document.getElementById('connection-overlay');
        const qrWrap = document.getElementById('qr-wrapper');
        const loadingWrap = document.getElementById('loading-wrapper');
        const statusText = document.getElementById('status-text');
        const btnReconnect = document.getElementById('btn-reconnect');

        overlay.classList.remove('d-none');
        document.getElementById('chat-interface').classList.add('d-none');

        if (type === 'QR') {
            qrWrap.classList.remove('d-none');
            loadingWrap.classList.add('d-none');
        } else {
            qrWrap.classList.add('d-none');
            loadingWrap.classList.remove('d-none');
            statusText.textContent = "Koneksi Terputus. Silakan hubungkan ulang.";
            btnReconnect.classList.remove('d-none');
        }
    }

    async function fetchQR() {
        try {
            const res = await fetch(URLS.qr);
            if (res.ok) {
                const data = await res.json();
                document.getElementById('qrcode').innerHTML = "";
                new QRCode(document.getElementById('qrcode'), { text: data.qr, width: 256, height: 256 });
            }
        } catch (e) { console.error(e); }
    }

    async function triggerReconnect() {
        try {
            await fetch(URLS.reconnect, { 
                method: 'POST', 
                headers: {'X-CSRF-TOKEN': CSRF_TOKEN} 
            });
            location.reload();
        } catch (e) { alert('Gagal reconnect'); }
    }

    // 2. LOGIKA CHAT
    async function openChat(roomId, element) {
        // UI Active State
        document.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        // Tampilkan panel chat
        document.getElementById('no-chat-selected').classList.add('d-none');
        document.getElementById('active-chat-wrapper').classList.remove('d-none');
        document.getElementById('active-chat-wrapper').classList.add('d-flex');

        activeRoomId = roomId;
        document.getElementById('active-room-id').value = roomId;
        
        // Loading state
        document.getElementById('messages-box').innerHTML = '<div class="text-center mt-5 text-muted">Memuat pesan...</div>';

        await loadMessages(roomId);
        
        // Start Polling Pesan Baru
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(() => loadMessages(roomId, true), 3000);
    }

    async function loadMessages(roomId, isPolling = false) {
        if (activeRoomId !== roomId) return;

        try {
            const url = URLS.getChat.replace(':id', roomId);
            const res = await fetch(url);
            const data = await res.json();

            // Update Header & Profil (Hanya sekali saat bukan polling, atau mau update status online)
            if (!isPolling) {
                updateHeaderAndProfile(data.room);
            }

            renderMessages(data.messages);
        } catch (e) {
            console.error(e);
        }
    }

    function updateHeaderAndProfile(room) {
        const customer = room.customer || {};
        const initial = (customer.name || 'G').charAt(0).toUpperCase();

        // Header
        document.getElementById('header-name').textContent = customer.name || 'Guest';
        document.getElementById('header-avatar').textContent = initial;
        
        // Profile Panel
        document.getElementById('profile-name').textContent = customer.name || 'Guest';
        document.getElementById('profile-avatar').textContent = initial;
        document.getElementById('profile-phone').textContent = customer.phone || '-';
        document.getElementById('profile-email').textContent = customer.email || '-';
        document.getElementById('profile-address').textContent = customer.address || '-';
    }

    function renderMessages(messages) {
        const container = document.getElementById('messages-box');
        
        // Cek posisi scroll user (biar gak loncat kalau lagi baca chat atas)
        const isAtBottom = (container.scrollHeight - container.scrollTop) <= (container.clientHeight + 100);

        let html = '';
        messages.forEach(msg => {
            const isMe = msg.sender_type === 'user' || msg.sender_type === 'cs'; // Sesuaikan dengan logic DB Anda
            const align = isMe ? 'outgoing' : 'incoming';
            const bubble = isMe ? 'out' : 'in';
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

            html += `
                <div class="msg-row ${align}">
                    <div class="msg-bubble ${bubble}">
                        ${msg.message_content}
                        <div class="msg-time">${time}</div>
                    </div>
                </div>
            `;
        });

        // Simple diff check: kalau HTML sama, jangan update biar gak flicker
        if (container.innerHTML !== html) {
            container.innerHTML = html;
            if (isAtBottom || messages.length === 0) {
                scrollToBottom();
            }
        }
    }

    async function sendMessage() {
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        const roomId = activeRoomId;

        if (!message || !roomId) return;

        // 1. Optimistic UI
        const container = document.getElementById('messages-box');
        const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        container.insertAdjacentHTML('beforeend', `
            <div class="msg-row outgoing opacity-75">
                <div class="msg-bubble out">
                    ${message}
                    <div class="msg-time">${time} <i class="bi bi-clock"></i></div>
                </div>
            </div>
        `);
        scrollToBottom();
        input.value = '';

        // 2. Kirim Server
        try {
            const url = URLS.sendChat.replace(':id', roomId);
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ message_body: message })
            });
            
            const json = await res.json();
            if (!json.success) throw new Error(json.error);

            // Refresh pesan untuk dapat status 'sent' yang valid
            loadMessages(roomId, true);

        } catch (e) {
            alert("Gagal kirim: " + e.message);
        }
    }

    function handleEnter(e) {
        if (e.key === 'Enter') sendMessage();
    }

    function scrollToBottom() {
        const container = document.getElementById('messages-box');
        container.scrollTop = container.scrollHeight;
    }

    function toggleProfile() {
        const panel = document.getElementById('profile-panel');
        if (panel.style.display === 'none') panel.style.display = 'flex';
        else panel.style.display = 'none';
    }
</script>

@endsection