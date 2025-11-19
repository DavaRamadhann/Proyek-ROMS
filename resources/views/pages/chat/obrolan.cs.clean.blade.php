@extends('layout.cs_main')

@section('title', 'Obrolan Pelanggan - ROMS')


@push('styles')
<style>
    /* Hapus padding default dari .main-content HANYA di halaman ini */
    .main-content {
        padding: 0 !important;
        height: calc(100vh - var(--topbar-height));
        display: flex;
        flex-direction: column;
    }

    .chat-container {
        display: flex;
        flex-grow: 1; 
        overflow: hidden; 
        height: 100%;
    }

    /* === Kolom Kiri (Daftar Chat) === */
    .chat-list-panel {
        width: 320px;
        flex-shrink: 0;
        border-right: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        background-color: #fff;
    }
    .chat-list-header {
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
    }
    .chat-list-body {
        overflow-y: auto;
        flex-grow: 1;
    }
    .chat-list-item {
        display: flex; align-items: center; padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0; cursor: pointer;
        text-decoration: none; color: #333;
    }
    .chat-list-item:hover { background-color: #f8f9fa; }
    .chat-list-item.active {
        background-color: #fef8e7; /* Turunan Emas */
        border-left: 4px solid #FCB53B; /* Emas */
    }
    .chat-list-item .avatar {
        width: 45px; height: 45px; border-radius: 50%;
        background-color: #B45253; /* Maroon */
        color: white; display: flex; align-items: center;
        justify-content: center; font-weight: 600;
        margin-right: 12px; flex-shrink: 0;
    }
    .chat-list-item .chat-info { overflow: hidden; }
    .chat-list-item .chat-info .name { font-weight: 600; margin-bottom: 2px; }
    .chat-list-item .chat-info .last-message {
        font-size: 0.85rem; color: #6c757d; white-space: nowrap;
        overflow: hidden; text-overflow: ellipsis;
    }
    .chat-list-item .chat-time {
        font-size: 0.75rem; color: #999;
        margin-left: auto; flex-shrink: 0;
    }


    /* === Kolom Tengah (Jendela Chat) === */
    .chat-active-panel {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background-color: #f4f7f6;
    }
    .chat-active-header {
        padding: 15px 20px; border-bottom: 1px solid #dee2e6;
        background-color: #fff; display: flex; align-items: center;
    }
    .chat-active-header .avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background-color: #B45253; color: white; display: flex;
        align-items: center; justify-content: center;
        font-weight: 600; margin-right: 12px;
    }
    .chat-active-header .name { font-weight: 600; font-size: 1.1rem; }
    .chat-body { flex-grow: 1; overflow-y: auto; padding: 20px; }
    .chat-bubble {
        max-width: 70%; padding: 10px 15px;
        border-radius: 15px; margin-bottom: 10px; clear: both;
    }
    .chat-bubble.customer { float: left; background-color: #ffffff; border: 1px solid #eee; }
    .chat-bubble.cs { float: right; background-color: #84994F; color: white; } /* Hijau */
    .chat-bubble .time {
        font-size: 0.75rem; color: #999;
        margin-top: 5px; display: block;
    }
    .chat-bubble.cs .time { color: #eaf0dc; }
    .chat-footer {
        padding: 15px 20px; background-color: #fff;
        border-top: 1px solid #dee2e6;
    }
    .btn-maroon {
        background-color: #B45253; border-color: #B45253; color: white;
    }
    .btn-maroon:hover { background-color: #9a4243; border-color: #9a4243; }


    /* =================================================
    CSS BARU UNTUK PROFIL (KOLOM KANAN)
    ================================================= */
    .chat-profile-panel {
        width: 320px; /* Lebar kolom profil */
        flex-shrink: 0;
        border-left: 1px solid #dee2e6;
        background-color: #ffffff;
        display: flex;
        flex-direction: column;
        height: 100%; /* Penting untuk layout flex */
    }
    .chat-profile-header {
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }
    .chat-profile-header .avatar {
        width: 80px; height: 80px; border-radius: 50%;
        background-color: #B45253; /* Maroon */
        color: white; margin: 0 auto 15px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: 600;
    }
    .chat-profile-header .name {
        font-size: 1.2rem;
        font-weight: 700;
    }
    .chat-profile-body {
        padding: 20px;
        overflow-y: auto;
        flex-grow: 1;
    }
    .chat-profile-body h6 {
        font-weight: 700;
        color: #B45253; /* Maroon */
        margin-top: 20px;
        margin-bottom: 15px;
    }
    /* Style untuk item riwayat pesanan */
    .order-history-item {
        display: flex;
        gap: 15px;
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .order-history-item .icon {
        font-size: 1.5rem;
        color: #84994F; /* Hijau */
    }
    .order-history-item .details {
        font-size: 0.9rem;
        overflow: hidden; /* Mencegah teks terlalu panjang */
    }
    .order-history-item .details .order-id {
        font-weight: 700;
        color: #333;
    }
    /* Badge Status */
    .badge-custom-success {
        background-color: #84994F; /* Hijau */
        color: white;
    }
    .badge-custom-danger {
        background-color: #B45253; /* Maroon */
        color: white;
    }
    /* ================================================= */
</style>

<style>
    /* --- RESET LAYOUT KHUSUS HALAMAN CHAT --- */
    /* Sembunyikan scrollbar window utama agar tidak ada double scroll */
    body { overflow: hidden; } 

    /* Wrapper Utama: Mengapung di atas layout dashboard bawaan */
    .wa-fullscreen-wrapper {
        position: fixed;
        top: 60px; /* Sesuaikan dengan tinggi Navbar kamu */
        left: 0;
        width: 100vw;
        height: calc(100vh - 60px);
        background-color: #d1d7db;
        z-index: 9999; /* Pastikan di atas sidebar/footer dashboard */
        display: flex;
        justify-content: center;
        padding: 0; /* Reset padding */
    }

    /* Jika sidebar dashboard kamu lebar, mungkin perlu left: 250px. 
       Tapi untuk amannya kita timpa saja (full screen total) */

    /* Container Chat ala WA Web */
    .wa-container {
        width: 100%;
        height: 100%;
        max-width: 1920px; /* Full width di layar besar */
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
        background-color: #efe7dd; /* Warna dasar WA */
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); /* Doodle WA */
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
        float: right; margin-left: 10px; margin-top: 4px;
        font-size: 10px; color: #667781; display: flex; align-items: center;
    }

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





@section('main-content')

<div class="chat-container">

    <aside class="chat-list-panel">
        <div class="chat-list-header">
            <h5 class="fw-bold">Obrolan (5)</h5>
            <input type="text" class="form-control" placeholder="Cari pelanggan...">
        </div>
        <div class="chat-list-body">
            <a href="#" class="chat-list-item active">
                <div class="avatar">AS</div>
                <div class="chat-info">
                    <div class="name">Ahmad Subagja</div>
                    <div class="last-message">Paket saya kok belum sampai ya?</div>
                </div>
                <div class="chat-time">5m</div>
            </a>
            <a href="#" class="chat-list-item">
                <div class="avatar">SL</div>
                <div class="chat-info">
                    <div class="name">Siti Lestari</div>
                    <div class="last-message">Terima kasih bantuannya kak!</div>
                </div>
                <div class="chat-time">2h</div>
            </a>
            <a href="#" class="chat-list-item">
                <div class="avatar">BH</div>
                <div class="chat-info">
                    <div class="name">Budi Hartono</div>
                    <div class="last-message">Oke saya tunggu update resinya.</div>
                </div>
                <div class="chat-time">1d</div>
            </a>
            </div>
    </aside>

    <section class="chat-active-panel">
        <header class="chat-active-header">
            <div class="avatar">AS</div>
            <div class="name">Ahmad Subagja</div>
        </header>

        <div class="chat-body">
            <div class="chat-bubble customer">
                Halo kak, pesanan saya #12345 kok belum sampai ya?
                <span class="time">10:30</span>
            </div>
            <div class="chat-bubble cs">
                Halo kak Ahmad, selamat siang. 
                Mohon ditunggu, saya cek dulu ya untuk pesanannya.
                <span class="time">10:31</span>
            </div>
            <div class="chat-bubble customer">
                Oke ditunggu kak.
                <span class="time">10:31</span>
            </div>
            <div class="chat-bubble cs">
                Baik kak, setelah dicek statusnya sudah "Dalam Pengiriman" ya. 
                Kemungkinan akan sampai hari ini atau besok.
                <span class="time">10:33</span>
            </div>
        </div>

        <footer class="chat-footer">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Ketik pesan Anda...">
                <button class="btn btn-maroon" type="button">
                    <i class="bi bi-send-fill me-1"></i> Kirim
                </button>
            </div>
        </footer>
    </section>

    <aside class="chat-profile-panel">
        <header class="chat-profile-header">
            <div class="avatar">AS</div>
            <div class="name">Ahmad Subagja</div>
            <small class="text-muted">ahmad.subagja@dummy.com</small>
        </header>

        <div class="chat-profile-body">
            <h6>Info Kontak (Dummy)</h6>
            <p class="mb-1">
                <strong class="text-muted" style="width: 80px; display: inline-block;">Telepon:</strong> 
                08123456789
            </p>
            <p class="mb-1">
                <strong class="text-muted" style="width: 80px; display: inline-block;">Alamat:</strong> 
                Jl. Dummy No. 123
            </p>

            <h6>Riwayat Pesanan (Dummy)</h6>
            
            <div class="order-history-item">
                <div class="icon"><i class="bi bi-box-seam-fill"></i></div>
                <div class="details">
                    <span class="order-id">#12345</span> 
                    <span class="badge badge-custom-success">Dalam Pengiriman</span>
                    <div class="text-muted">10 Nov 2025</div>
                    <div>Rp 150.000</div>
                </div>
            </div>
            
            <div class="order-history-item">
                <div class="icon"><i class="bi bi-box-seam-fill"></i></div>
                <div class="details">
                    <span class="order-id">#12001</span> 
                    <span class="badge badge-custom-success">Selesai</span>
                    <div class="text-muted">01 Okt 2025</div>
                    <div>Rp 85.000</div>
                </div>
            </div>

            <div class="order-history-item">
                <div class="icon"><i class="bi bi-x-circle-fill text-danger"></i></div>
                <div class="details">
                    <span class="order-id">#11950</span> 
                    <span class="badge badge-custom-danger">Dibatalkan</span>
                    <div class="text-muted">28 Sep 2025</div>
                    <div>Rp 210.000</div>
                </div>
            </div>

        </div>
    </aside>

</div>



<script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
<script>
    // --- KONFIGURASI URL (PENTING: Generate pakai Blade biar ga 404) ---
    const URLS = {
        status: "{{ route('admin.whatsapp.api.status') }}",
        qr: "{{ route('admin.whatsapp.api.qr') }}",
        reconnect: "{{ route('admin.whatsapp.api.reconnect') }}",
        // Kita buat template URL untuk chat, nanti :id diganti via JS
        getChat: "{{ url('/chat/room/:id/data') }}", 
        sendChat: "{{ url('/chat/room/:id/send-ajax') }}"
    };

    const CSRF_TOKEN = "{{ csrf_token() }}";
    
    let currentStatus = 'INIT';
    let activeRoomId = null;
    let pollingChatInterval = null;
    let qrCodeObj = null;

    // --- 1. LOGIKA KONEKSI (WAJIB JALAN DULUAN) ---
    document.addEventListener('DOMContentLoaded', () => {
        checkConnection();
        // Cek status berkala tiap 5 detik
        setInterval(checkConnection, 5000); 
    });

    async function checkConnection() {
        if (currentStatus === 'READY' && !activeRoomId) return; // Hemat request jika sudah ready dan belum buka chat

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

        // Reset
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
            // Auto reconnect
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
            el.innerHTML = ''; // Clear lama
            new QRCode(el, { text: data.qr, width: 200, height: 200 });
        } catch (e) { console.error(e); }
    }


    // --- 2. LOGIKA CHAT (KIRIM & TERIMA) ---

    async function openChat(roomId, el) {
        // Highlight sidebar
        document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
        el.classList.add('active');

        activeRoomId = roomId;
        document.getElementById('room-id-active').value = roomId;

        // Tampilkan area chat
        document.getElementById('view-placeholder').classList.add('d-none');
        document.getElementById('view-chat').classList.remove('d-none');
        document.getElementById('view-chat').classList.add('d-flex');

        // Reset Chat Area
        document.getElementById('messages-box').innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-secondary spinner-border-sm"></div></div>';

        // Ambil Data Awal
        await fetchMessages(roomId);

        // Set Nama & Avatar di Header Chat
        const name = el.querySelector('.chat-name').textContent;
        document.getElementById('chat-name').textContent = name;
        document.getElementById('chat-avatar').textContent = name.charAt(0);
        document.getElementById('chat-avatar').style.backgroundColor = el.querySelector('.avatar-circle').style.backgroundColor;

        // Mulai Polling Pesan (Tiap 2 detik)
        if (pollingChatInterval) clearInterval(pollingChatInterval);
        pollingChatInterval = setInterval(() => fetchMessages(roomId), 2000);
    }

    async function fetchMessages(roomId) {
        if (activeRoomId != roomId) return; // Cegah race condition

        try {
            // Replace placeholder :id dengan ID asli
            const url = URLS.getChat.replace(':id', roomId);
            const res = await fetch(url);
            
            if (!res.ok) throw new Error("Gagal load chat");
            
            const data = await res.json();
            document.getElementById('chat-phone').textContent = data.room.customer.phone; // Update nomor HP

            renderMessages(data.messages);

        } catch (e) {
            console.error(e);
        }
    }

    function renderMessages(messages) {
        const container = document.getElementById('messages-box');
        
        // Cek apakah user sedang scroll di atas (biar ga loncat kalau lagi baca chat lama)
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
            
            // Icon status simulasi (bisa dikonekkan ke DB nanti)
            const check = isMe ? '<i class="bi bi-check2-all text-primary ms-1"></i>' : '';

            html += `
                <div class="msg-row ${align}">
                    <div class="msg-bubble ${bubble}">
                        ${msg.message_content}
                        <div class="msg-time">${time} ${check}</div>
                    </div>
                </div>
            `;
        });

        // Ganti isi container (bisa dioptimasi dengan diffing, tapi ini cukup untuk skrg)
        // Cek dulu biar ga flicker parah kalau datanya sama
        // (Disini kita langsung timpa saja demi kesederhanaan)
        container.innerHTML = html;

        // Auto Scroll Down kalau user ada di bawah
        if (isUserAtBottom) {
            container.scrollTop = container.scrollHeight;
        }
    }

    async function sendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('input-message');
        const message = input.value.trim();
        const roomId = document.getElementById('room-id-active').value;

        if (!message || !roomId) return;

        // 1. Optimistic UI (Langsung muncul)
        const container = document.getElementById('messages-box');
        const time = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
        
        container.insertAdjacentHTML('beforeend', `
            <div class="msg-row outgoing opacity-50">
                <div class="msg-bubble out">
                    ${message}
                    <div class="msg-time">${time} <i class="bi bi-clock ms-1"></i></div>
                </div>
            </div>
        `);
        container.scrollTop = container.scrollHeight;
        input.value = '';

        // 2. Kirim ke Server
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

            // Sukses: Refresh pesan agar dapat data 'resmi' dari server (jam, id, dll)
            fetchMessages(roomId);

        } catch (err) {
            console.error("Error kirim:", err);
            alert("Gagal mengirim pesan: " + err.message);
            // Opsional: Hapus bubble optimistic atau beri tanda merah
        }
    }
</script>

@endsection