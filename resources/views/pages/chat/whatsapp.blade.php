@extends('layouts.app')

@section('title', 'WhatsApp Chat')

@push('styles')
<style>
    /* --- RESET LAYOUT KHUSUS HALAMAN CHAT --- */
    body { overflow: hidden; } 

    /* Wrapper Utama */
    .wa-fullscreen-wrapper {
        position: fixed;
        top: 60px; 
        left: 0;
        width: 100vw;
        height: calc(100vh - 60px);
        background-color: #d1d7db;
        z-index: 9999; 
        display: flex;
        justify-content: center;
        padding: 0; 
    }

    /* Container Chat */
    .wa-container {
        width: 100%;
        height: 100%;
        max-width: 1920px; 
        background: #fff;
        display: flex;
        overflow: hidden;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    /* --- SIDEBAR KIRI --- */
    .wa-sidebar {
        width: 350px;
        flex-shrink: 0;
        background: #fff;
        border-right: 1px solid #e9edef;
        display: flex;
        flex-direction: column;
    }

    .wa-header-left {
        height: 60px;
        background: #f0f2f5;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #d1d7db;
    }

    .wa-chat-list {
        flex: 1;
        overflow-y: auto;
        background: #fff;
    }

    /* Item Chat */
    .chat-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f2f5;
        transition: background-color 0.2s;
    }
    .chat-item:hover { background-color: #f5f6f6; }
    .chat-item.active { background-color: #f0f2f5; }

    .avatar-circle {
        width: 45px; height: 45px;
        border-radius: 50%;
        background: #dfe5e7;
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 18px;
        margin-right: 15px;
    }

    .chat-info { flex: 1; min-width: 0; }
    .chat-name { font-size: 16px; color: #111b21; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .chat-preview { font-size: 13px; color: #667781; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .chat-meta { font-size: 11px; color: #8696a0; margin-left: 10px; }

    /* --- AREA CHAT KANAN --- */
    .wa-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: #efe7dd; 
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); 
        background-repeat: repeat; 
        position: relative;
    }

    .wa-header-main {
        height: 60px;
        background: #f0f2f5;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #d1d7db;
        z-index: 10;
    }

    .wa-messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 20px 5%;
        display: flex;
        flex-direction: column;
    }

    /* Bubble Chat */
    .msg-row { display: flex; margin-bottom: 8px; }
    .msg-row.outgoing { justify-content: flex-end; }
    .msg-row.incoming { justify-content: flex-start; }

    .msg-bubble {
        max-width: 65%;
        padding: 6px 10px 8px 10px;
        border-radius: 8px;
        position: relative;
        font-size: 14px;
        line-height: 19px;
        box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
    }

    .msg-bubble.in { background: #fff; border-top-left-radius: 0; }
    .msg-bubble.out { background: #d9fdd3; border-top-right-radius: 0; }

    .msg-time {
        display: block;
        text-align: right;
        margin-top: 4px;
        font-size: 10px; color: #667781;
    }
    .msg-time i { margin-left: 4px; }

    /* Footer Input */
    .wa-footer {
        min-height: 62px;
        background: #f0f2f5;
        padding: 10px 16px;
        display: flex; align-items: center; gap: 10px;
        z-index: 10;
    }
    .wa-input {
        flex: 1;
        border: none; border-radius: 8px;
        padding: 10px 12px; font-size: 15px;
        outline: none;
    }

    /* Utils */
    .d-none { display: none !important; }
    .placeholder-screen {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        height: 100%; text-align: center; color: #41525d;
        background: #f0f2f5; border-bottom: 6px solid #25d366;
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
@endpush

@section('content')

{{-- WRAPPER UTAMA (FULL SCREEN) --}}
<div class="wa-fullscreen-wrapper">
    
    {{-- 1. PANEL KONEKSI --}}
    <div id="connection-panel" class="card shadow-lg border-0 p-4 align-self-center d-none" style="width: 400px;">
        <div class="text-center">
            <h4 class="mb-3">Koneksi WhatsApp</h4>
            
            <div id="qr-wrapper" class="d-none mb-3">
                <div id="qrcode" class="d-flex justify-content-center"></div>
                <small class="text-muted mt-2 d-block">Scan QR di Menu > Perangkat Tertaut</small>
            </div>

            <div id="loading-wrapper" class="my-3">
                <div class="spinner-border text-success mb-2" role="status"></div>
                <p class="text-muted small" id="loading-text">Memeriksa status...</p>
            </div>

            <span id="status-badge" class="badge bg-secondary">...</span>
        </div>
    </div>

    {{-- 2. INTERFACE CHAT --}}
    <div id="chat-interface" class="wa-container d-none">
        
        {{-- SIDEBAR --}}
        <div class="wa-sidebar">
            <div class="wa-header-left">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary me-2" style="width:40px;height:40px;font-size:14px;">CS</div>
                    <span class="fw-bold">Chat</span>
                </div>
                <div>
                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                </div>
            </div>

            {{-- LIST ROOM --}}
            <div class="wa-chat-list">
                @forelse ($rooms as $room)
                    <div class="chat-item" onclick="openChat({{ $room->id }}, this)">
                        <div class="avatar-circle" style="background-color: {{ $loop->iteration % 2 == 0 ? '#009688' : '#3f51b5' }}">
                            {{ strtoupper(substr($room->customer->name ?? 'G', 0, 1)) }}
                        </div>
                        <div class="chat-info">
                            <div class="d-flex justify-content-between">
                                <div class="chat-name fw-bold">
                                    @php
                                        $rawName = $room->customer->name ?? 'Guest';
                                        $phone = $room->customer->phone ?? '-';
                                        $displayName = $rawName;
                                        if (str_contains($rawName, '@c.us') || str_contains($rawName, '@g.us')) {
                                            $displayName = $phone;
                                        }
                                    @endphp
                                    {{ $displayName }}
                                </div>
                                <div class="chat-meta">{{ $room->updated_at->format('H:i') }}</div>
                            </div>
                            {{-- ADDED: Phone Number Display --}}
                            <div class="small text-muted mb-1" style="font-size: 11px;">{{ $phone }}</div>

                            <div class="chat-preview">
                                @if($room->messages->last())
                                    @if($room->messages->last()->sender_type == 'user')
                                        <i class="bi bi-check2 text-secondary"></i>
                                    @endif
                                    {{ Str::limit($room->messages->last()->message_content, 30) }}
                                @else
                                    <span class="fst-italic text-muted">Belum ada pesan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted small">Belum ada chat.</div>
                @endforelse
            </div>
        </div>

        {{-- MAIN AREA --}}
        <div class="wa-main">
            {{-- Placeholder --}}
            <div id="view-placeholder" class="placeholder-screen">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/150px-WhatsApp.svg.png" width="80" class="mb-4 opacity-50">
                <h3>WhatsApp for ROMS</h3>
                <p>Kirim dan terima pesan tanpa perlu buka HP.<br>Pilih chat di sebelah kiri untuk mulai.</p>
            </div>

            {{-- Chat View --}}
            <div id="view-chat" class="d-none flex-column h-100">
                {{-- Header --}}
                <div class="wa-header-main">
                    <div class="avatar-circle me-3" id="chat-avatar" style="width:40px;height:40px;font-size:16px;">U</div>
                    <div class="d-flex flex-column">
                        <span class="fw-bold" id="chat-name">Nama User</span>
                        <small class="text-muted" id="chat-phone">+62...</small>
                    </div>
                </div>

                {{-- Bubble Messages --}}
                <div class="wa-messages-container" id="messages-box">
                    {{-- Pesan akan di-inject via JS --}}
                </div>

                {{-- Input --}}
                <div class="wa-footer">
                    <i class="bi bi-emoji-smile fs-4 text-secondary" style="cursor:pointer"></i>
                    <i class="bi bi-paperclip fs-4 text-secondary mx-2" style="cursor:pointer"></i>
                    
                    <form id="form-send" class="d-flex flex-grow-1" onsubmit="sendMessage(event)">
                        <input type="hidden" id="room-id-active">
                        <textarea id="input-message" class="wa-input" rows="1" placeholder="Ketik pesan" style="resize:none; overflow-y:hidden;"></textarea>
                        <button type="submit" class="btn p-0 ms-3 border-0 bg-transparent">
                            <i class="bi bi-send-fill fs-4 text-secondary"></i>
                        </button>
                    </form>
                </div>
            </div>
```html
    .wa-header-main {
        height: 60px;
        background: #f0f2f5;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #d1d7db;
        z-index: 10;
    }

    .wa-messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 20px 5%;
        display: flex;
        flex-direction: column;
    }

    /* Bubble Chat */
    .msg-row { display: flex; margin-bottom: 8px; }
    .msg-row.outgoing { justify-content: flex-end; }
    .msg-row.incoming { justify-content: flex-start; }

    .msg-bubble {
        max-width: 65%;
        padding: 6px 10px 8px 10px;
        border-radius: 8px;
        position: relative;
        font-size: 14px;
        line-height: 19px;
        box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
    }

    .msg-bubble.in { background: #fff; border-top-left-radius: 0; }
    .msg-bubble.out { background: #d9fdd3; border-top-right-radius: 0; }

    .msg-time {
        display: block;
        text-align: right;
        margin-top: 4px;
        font-size: 10px; color: #667781;
    }
    .msg-time i { margin-left: 4px; }

    /* Footer Input */
    .wa-footer {
        min-height: 62px;
        background: #f0f2f5;
        padding: 10px 16px;
        display: flex; align-items: center; gap: 10px;
        z-index: 10;
    }
    .wa-input {
        flex: 1;
        border: none; border-radius: 8px;
        padding: 10px 12px; font-size: 15px;
        outline: none;
    }

    /* Utils */
    .d-none { display: none !important; }
    .placeholder-screen {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        height: 100%; text-align: center; color: #41525d;
        background: #f0f2f5; border-bottom: 6px solid #25d366;
    }
</style>

@section('content')

{{-- WRAPPER UTAMA (FULL SCREEN) --}}
<div class="wa-fullscreen-wrapper">
    
    {{-- 1. PANEL KONEKSI --}}
    <div id="connection-panel" class="card shadow-lg border-0 p-4 align-self-center d-none" style="width: 400px;">
        <div class="text-center">
            <h4 class="mb-3">Koneksi WhatsApp</h4>
            
            <div id="qr-wrapper" class="d-none mb-3">
                <div id="qrcode" class="d-flex justify-content-center"></div>
                <small class="text-muted mt-2 d-block">Scan QR di Menu > Perangkat Tertaut</small>
            </div>

            <div id="loading-wrapper" class="my-3">
                <div class="spinner-border text-success mb-2" role="status"></div>
                <p class="text-muted small" id="loading-text">Memeriksa status...</p>
            </div>

            <span id="status-badge" class="badge bg-secondary">...</span>
        </div>
    </div>

    {{-- 2. INTERFACE CHAT --}}
    <div id="chat-interface" class="wa-container d-none">
        
        {{-- SIDEBAR --}}
        <div class="wa-sidebar">
            <div class="wa-header-left">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary me-2" style="width:40px;height:40px;font-size:14px;">CS</div>
                    <span class="fw-bold">Chat</span>
                </div>
                <div>
                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                </div>
            </div>

            {{-- LIST ROOM --}}
            <div class="wa-chat-list">
                @forelse ($rooms as $room)
                    <div class="chat-item" onclick="openChat({{ $room->id }}, this)">
                        <div class="avatar-circle" style="background-color: {{ $loop->iteration % 2 == 0 ? '#009688' : '#3f51b5' }}">
                            {{ strtoupper(substr($room->customer->name ?? 'G', 0, 1)) }}
                        </div>
                        <div class="chat-info">
                            <div class="d-flex justify-content-between">
                                <div class="chat-name fw-bold">
                                    @php
                                        $rawName = $room->customer->name ?? 'Guest';
                                        $phone = $room->customer->phone ?? '-';
                                        $displayName = $rawName;
                                        if (str_contains($rawName, '@c.us') || str_contains($rawName, '@g.us')) {
                                            $displayName = $phone;
                                        }
                                    @endphp
                                    {{ $displayName }}
                                </div>
                                <div class="chat-meta">{{ $room->updated_at->format('H:i') }}</div>
                            </div>
                            {{-- ADDED: Phone Number Display --}}
                            <div class="small text-muted mb-1" style="font-size: 11px;">{{ $phone }}</div>

                            <div class="chat-preview">
                                @if($room->messages->last())
                                    @if($room->messages->last()->sender_type == 'user')
                                        <i class="bi bi-check2 text-secondary"></i>
                                    @endif
                                    {{ Str::limit($room->messages->last()->message_content, 30) }}
                                @else
                                    <span class="fst-italic text-muted">Belum ada pesan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted small">Belum ada chat.</div>
                @endforelse
            </div>
        </div>

        {{-- MAIN AREA --}}
        <div class="wa-main">
            {{-- Placeholder --}}
            <div id="view-placeholder" class="placeholder-screen">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/150px-WhatsApp.svg.png" width="80" class="mb-4 opacity-50">
                <h3>WhatsApp for ROMS</h3>
                <p>Kirim dan terima pesan tanpa perlu buka HP.<br>Pilih chat di sebelah kiri untuk mulai.</p>
            </div>

            {{-- Chat View --}}
            <div id="view-chat" class="d-none flex-column h-100">
                {{-- Header --}}
                <div class="wa-header-main">
                    <div class="avatar-circle me-3" id="chat-avatar" style="width:40px;height:40px;font-size:16px;">U</div>
                    <div class="d-flex flex-column">
                        <span class="fw-bold" id="chat-name">Nama User</span>
                        <small class="text-muted" id="chat-phone">+62...</small>
                    </div>
                </div>

                {{-- Bubble Messages --}}
                <div class="wa-messages-container" id="messages-box">
                    {{-- Pesan akan di-inject via JS --}}
                </div>

                {{-- Input --}}
                <div class="wa-footer">
                    <i class="bi bi-emoji-smile fs-4 text-secondary" style="cursor:pointer"></i>
                    <i class="bi bi-paperclip fs-4 text-secondary mx-2" style="cursor:pointer"></i>
                    
                    <form id="form-send" class="d-flex flex-grow-1" onsubmit="sendMessage(event)">
                        <input type="hidden" id="room-id-active">
                        <textarea id="input-message" class="wa-input" rows="1" placeholder="Ketik pesan" style="resize:none; overflow-y:hidden;"></textarea>
                        <button type="submit" class="btn p-0 ms-3 border-0 bg-transparent">
                            <i class="bi bi-send-fill fs-4 text-secondary"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
<script>
    console.log(">>> SCRIPT LOADED <<<"); // Global Debug

    // Create indicator immediately
    (function() {
        const indicator = document.createElement('div');
        indicator.id = 'polling-indicator';
        indicator.style.cssText = 'position:fixed; top:10px; right:10px; font-size:12px; color:white; background:red; z-index:9999; padding:5px; border-radius:4px; font-weight:bold;';
        indicator.textContent = 'JS ACTIVE';
        document.body.appendChild(indicator);
    })();

    const URLS = {
        status: "{{ route('admin.whatsapp.api.status') }}",
        qr: "{{ route('admin.whatsapp.api.qr') }}",
        reconnect: "{{ route('admin.whatsapp.api.reconnect') }}",
        getChat: "{{ url('/chat/room/:id/data') }}", 
        sendChat: "{{ url('/chat/room/:id/send-ajax') }}"
    };

    const CSRF_TOKEN = "{{ csrf_token() }}";
    
    let currentStatus = 'INIT';
    let activeRoomId = null;
    let pollingChatInterval = null;

    document.addEventListener('DOMContentLoaded', () => {
        console.log("DOM LOADED");
        checkConnection();
        setInterval(checkConnection, 5000); 
    });

    async function checkConnection() {
        if (currentStatus === 'READY' && !activeRoomId) return; 

        try {
            const res = await fetch(URLS.status);
            const data = await res.json();
            updateUI(data.status);
        } catch (e) {
            console.error("Gagal cek status:", e);
        }
    }

    function updateUI(status) {
        currentStatus = status;
        const panel = document.getElementById('connection-panel');
        const chatUI = document.getElementById('chat-interface');
        const badge = document.getElementById('status-badge');
        const qrWrap = document.getElementById('qr-wrapper');
        const loadWrap = document.getElementById('loading-wrapper');
        const loadText = document.getElementById('loading-text');

        if (status === 'READY') {
            panel.classList.add('d-none');
            chatUI.classList.remove('d-none');
        } else {
            panel.classList.remove('d-none');
            chatUI.classList.add('d-none');
        }

        qrWrap.classList.add('d-none');
        loadWrap.classList.add('d-none');

        if (status === 'QR') {
            badge.textContent = 'SCAN QR'; badge.className = 'badge bg-warning text-dark';
            qrWrap.classList.remove('d-none');
            fetchQR();
        } else if (['AUTHENTICATED', 'INITIALIZING'].includes(status)) {
            badge.textContent = 'CONNECTING...'; badge.className = 'badge bg-info';
            loadWrap.classList.remove('d-none');
            loadText.textContent = 'Sinkronisasi WhatsApp...';
        } else if (['DISCONNECTED', 'UNKNOWN'].includes(status)) {
            badge.textContent = 'TERPUTUS'; badge.className = 'badge bg-danger';
            loadWrap.classList.remove('d-none');
            loadText.textContent = 'Koneksi terputus. Menghubungkan ulang...';
            fetch(URLS.reconnect, { method: 'POST', headers: {'X-CSRF-TOKEN': CSRF_TOKEN} });
        }
    }

    async function fetchQR() {
        if (currentStatus !== 'QR') return;
        try {
            const res = await fetch(URLS.qr);
            if (res.status === 404) { checkConnection(); return; }
            const data = await res.json();
            
            const el = document.getElementById('qrcode');
            el.innerHTML = ''; 
            new QRCode(el, { text: data.qr, width: 200, height: 200 });
        } catch (e) { console.error(e); }
    }


    // --- LOGIKA CHAT ---

    async function openChat(roomId, el) {
        document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
        el.classList.add('active');

        activeRoomId = roomId;
        document.getElementById('room-id-active').value = roomId;

        document.getElementById('view-placeholder').classList.add('d-none');
        document.getElementById('view-chat').classList.remove('d-none');
        document.getElementById('view-chat').classList.add('d-flex');

        document.getElementById('messages-box').innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-secondary spinner-border-sm"></div></div>';

        await fetchMessages(roomId);

        // Set Header
        const nameEl = el.querySelector('.chat-name');
        const name = nameEl ? nameEl.textContent.trim() : 'User';
        document.getElementById('chat-name').textContent = name;
        document.getElementById('chat-avatar').textContent = name.charAt(0);
        
        const avatarBg = el.querySelector('.avatar-circle').style.backgroundColor;
        document.getElementById('chat-avatar').style.backgroundColor = avatarBg;

        if (pollingChatInterval) clearInterval(pollingChatInterval);
        pollingChatInterval = setInterval(() => fetchMessages(roomId), 3000); 
    }

    async function fetchMessages(roomId) {
        console.log("Polling messages for room:", roomId, "at", new Date().toLocaleTimeString()); // Debugging
        
        const indicator = document.getElementById('polling-indicator');
        if (indicator) {
            indicator.style.background = 'green';
            indicator.textContent = 'Polling: ' + new Date().toLocaleTimeString();
        }

        if (activeRoomId != roomId) return; 

        try {
            // Add timestamp to prevent caching
            const url = URLS.getChat.replace(':id', roomId) + '?t=' + new Date().getTime();
            const res = await fetch(url);
            
            if (!res.ok) throw new Error("Gagal load chat");
            
            const data = await res.json();
            document.getElementById('chat-phone').textContent = data.room.customer.phone; 

            renderMessages(data.messages);

        } catch (e) {
            console.error(e);
            if (indicator) indicator.style.background = 'red';
        }
    }

    function renderMessages(messages) {
        const container = document.getElementById('messages-box');
        const isUserAtBottom = (container.scrollHeight - container.scrollTop) <= (container.clientHeight + 150);

        if (messages.length === 0) {
            container.innerHTML = '<div class="text-center mt-5"><span class="badge bg-white text-secondary shadow-sm p-2">Mulai percakapan</span></div>';
            return;
        }

        let html = '';
        messages.forEach(msg => {
            const isMe = msg.sender_type === 'user';
            const align = isMe ? 'outgoing' : 'incoming';
            const bubble = isMe ? 'out' : 'in';
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
            const check = isMe ? '<i class="bi bi-check2-all text-primary ms-1"></i>' : '';

            html += `
                <div class="msg-row ${align}">
                    <div class="msg-bubble ${bubble}">
                        ${msg.message_content}
                        <div class="msg-time">
                            ${time} ${check}
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        if (isUserAtBottom) {
            container.scrollTop = container.scrollHeight;
        }
    }

    async function sendMessage(e) {
        if(e) e.preventDefault(); // Handle if called via event
        const input = document.getElementById('input-message');
        const message = input.value.trim();
        const roomId = document.getElementById('room-id-active').value;

        if (!message || !roomId) return;

        const container = document.getElementById('messages-box');
        const time = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
        
        container.insertAdjacentHTML('beforeend', `
            <div class="msg-row outgoing opacity-50">
                <div class="msg-bubble out">
                    ${message}
                    <div class="msg-time">
                        ${time} <i class="bi bi-clock ms-1"></i>
                    </div>
                </div>
            </div>
        `);
        container.scrollTop = container.scrollHeight;
        input.value = '';

        try {
            const url = URLS.sendChat.replace(':id', roomId);
            
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message_body: message })
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                throw new Error(data.error || 'Gagal mengirim');
            }

            showSentNotification();

        } catch (err) {
            console.error("Error kirim:", err);
            alert("Gagal mengirim pesan: " + err.message);
        }
    }

    // Auto-resize & Enter
    const inputMsg = document.getElementById('input-message');
    if(inputMsg){
        inputMsg.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if(this.value === '') this.style.height = 'auto';
        });

        inputMsg.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage(e);
            }
        });
    }

    function showSentNotification() {
        let notif = document.getElementById('msg-sent-notif');
        if (!notif) {
            notif = document.createElement('div');
            notif.id = 'msg-sent-notif';
            notif.style.cssText = `
                position: fixed;
                bottom: 80px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0,0,0,0.7);
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                z-index: 9999;
                transition: opacity 0.3s;
            `;
            notif.innerHTML = '<i class="bi bi-check-circle me-1"></i> Pesan terkirim';
            document.body.appendChild(notif);
        }
        
        notif.style.opacity = '1';
        notif.style.display = 'block';

        setTimeout(() => {
            notif.style.opacity = '0';
            setTimeout(() => {
                notif.style.display = 'none';
            }, 300);
        }, 2000);
    }
</script>
@endpush
@endsection
```