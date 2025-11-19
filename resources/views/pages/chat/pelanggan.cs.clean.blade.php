@extends('layout.cs_main') {{-- Tetap pakai layout CS --}}

@section('title', 'Data Pelanggan - ROMS')


@push('styles')
<style>
    .dashboard-header {
        font-size: 1.75rem;
        font-weight: 700;
        color: #333;
    }
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    /* Tombol Aksi */
    .btn-aksi {
        font-size: 0.85rem;
        padding: 0.25rem 0.5rem;
    }

    /* Kustomisasi Badge Segmen (sesuai PDF) */
    .badge-custom-loyal {
        background-color: #84994F; /* Hijau (Loyal) */
        color: white;
    }
    .badge-custom-baru {
        background-color: #FCB53B; /* Emas (Baru) */
        color: #333; 
    }
    .badge-custom-aktif {
        background-color: #45b6e8; /* Biru (Aktif) */
        color: white;
    }
    .badge-custom-calon {
        background-color: #6c757d; /* Abu-abu (Calon) */
        color: white;
    }
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

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="dashboard-header">Data Pelanggan</h2>
    <div class="input-group" style="max-width: 400px;">
        <input type="text" class="form-control" placeholder="Cari nama, email, atau telepon...">
        <button class="btn btn-outline-secondary" type="button">
            <i class="bi bi-search"></i>
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Nama Pelanggan</th>
                    <th scope="col">Kontak</th>
                    <th scope="col">Tanggal Bergabung</th>
                    <th scope="col">Total Pesanan</th>
                    <th scope="col">Segmen</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
    @foreach($items as $item)
        {{-- Ganti sesuai struktur datamu --}}
    @endforeach
</tbody>
        </table>
    </div>
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

{{-- Helper CSS untuk Avatar Kecil --}}
@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
    flex-shrink: 0;
}
</style>
@endpush