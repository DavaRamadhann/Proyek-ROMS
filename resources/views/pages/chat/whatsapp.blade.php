@extends('layout.app')
{{-- 
  Kita extend 'layout.app' agar konsisten dengan 
  pages/chat/index.blade.php dan show.blade.php
--}}

@section('title', 'ROMS CS Chat Interface')

{{-- 
  Kita tambahkan CSS kustom di sini.
  Layout 'layout.app' membungkus @yield('content') di dalam <div class="container">.
  Ini akan mengganggu layout full-height 3-kolom kita.
  Oleh karena itu, kita perlu CSS override untuk container dan body.
--}}
<style>
    /* Override Bootstrap dan layout.app */
    body {
        background-color: #f0f2f5;
        /* Hentikan scroll di body, biarkan kolom yg scroll */
        overflow: hidden; 
    }
    main.py-5 {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .container {
        max-width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin: 0 !important;
    }

    /* Struktur 3-Kolom */
    .chat-layout-container {
        display: flex;
        /* Tinggi penuh viewport */
        height: 100vh; 
        background: #fff;
    }

    /* Kolom Umum */
    .chat-col {
        display: flex;
        flex-direction: column;
        height: 100%;
        border-right: 1px solid #e0e0e0;
    }
    .chat-col-header {
        padding: 1rem;
        background-color: #f0f2f5;
        border-bottom: 1px solid #e0e0e0;
        min-height: 65px;
        flex-shrink: 0;
    }
    .chat-col-body {
        flex-grow: 1;
        overflow-y: auto;
    }
    .chat-col-footer {
        padding: 0.75rem 1rem;
        background-color: #f0f2f5;
        border-top: 1px solid #e0e0e0;
        flex-shrink: 0;
    }

    /* Kolom Kiri - Daftar Chat */
    .chat-list-col {
        background-color: #ffffff;
    }
    .chat-list-body {
        padding: 0;
    }
    .chat-list-item {
        display: flex;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .chat-list-item:hover, .chat-list-item.active {
        background-color: #f5f5f5;
    }
    .chat-list-item img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .chat-list-content {
        flex-grow: 1;
        overflow: hidden; /* Penting untuk text-overflow */
    }
    .chat-list-content .name {
        font-weight: 600;
        color: #111;
    }
    .chat-list-content .message, .chat-list-content .time {
        font-size: 0.85rem;
        color: #666;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis; /* Tampilkan ... jika teks panjang */
    }
    
    /* Kolom Tengah - Area Chat */
    .chat-main-col {
        /* Latar belakang chat ala WA */
        background-color: #e5ddd5; 
    }
    .chat-main-body {
        padding: 1.5rem;
        /* Tiru style bubble dari show.blade.php */
        display: flex;
        flex-direction: column;
    }
    /* Placeholder jika chat kosong */
    .chat-empty-state {
        margin: auto;
        text-align: center;
        color: #555;
        background-color: #f7f7f7;
        padding: 2rem;
        border-radius: 8px;
        max-width: 400px;
    }

    /* Kolom Kanan - Detail */
    .chat-detail-col {
        background-color: #f7f7f7;
        border-right: none;
    }
    .chat-detail-body {
        padding: 1.5rem;
        text-align: center;
    }
    .chat-detail-body img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin-bottom: 1rem;
        border: 3px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Tombol Kirim Kustom (Hijau WA) */
    .btn-send {
        background-color: #25D366;
        border-color: #25D366;
        color: white;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-send:hover {
        background-color: #128C7E;
        border-color: #128C7E;
        color: white;
    }

</style>

@section('content')
<div class="chat-layout-container">
    
    <div class="col-12 col-md-3 chat-col chat-list-col">
        <div class="chat-col-header">
            <h5 class="mb-1">Daftar Chat</h5>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" placeholder="Cari atau mulai chat baru...">
                <select class="form-select flex-grow-0" style="width: 100px;">
                    <option value="all">Semua</option>
                    <option value="active">Aktif</option>
                    <option value="done">Selesai</option>
                </select>
            </div>
        </div>
        
        <div class="chat-col-body list-group list-group-flush" id="chat-list-panel">
            
            {{-- Loop dari $rooms yang dikirim ChatController --}}
            @forelse ($rooms as $room)
                {{-- 
                  Item ini akan di-handle oleh JS. 
                  data-room-id dipakai untuk fetch data via AJAX.
                --}}
                <a href="#" class="list-group-item list-group-item-action chat-list-item" data-room-id="{{ $room->id }}">
                    {{-- Placeholder Avatar --}}
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($room->customer->name ?? 'P') }}&background=random" alt="Avatar">
                    
                    <div class="chat-list-content">
                        <div class="d-flex justify-content-between">
                            <span class="name">{{ $room->customer->name ?? 'Nama Pelanggan' }}</span>
                            <span class="time small">
                                @if ($room->status == 'new')
                                    <span class="badge bg-danger">BARU</span>
                                @elseif ($room->status == 'open')
                                    <span class="badge bg-primary">Open</span>
                                @endif
                                    </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="message">
                                @if ($room->status == 'new')
                                    <span class="badge bg-danger">BARU</span>
                                @else
                                    {{-- Placeholder pesan terakhir --}}
                                    Klik untuk membuka chat...
                                @endif
                            </span>
                            {{-- <span class="badge bg-success rounded-pill">1</span> --}}
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center p-4 text-muted">
                    <small>Belum ada chat yang di-assign.</small>
                </div>
            @endforelse

        </div>
    </div>

    <div class="col-12 col-md-6 chat-col chat-main-col">
        <div class="chat-col-header d-flex align-items-center" id="chat-main-header">
            {{-- Konten di-load JS --}}
            <img src="https://ui-avatars.com/api/?name=?&background=e0e0e0&color=888" alt="Avatar" class="rounded-circle me-2" width="40" height="40">
            <div>
                <h6 class="mb-0">Pilih Chat</h6>
                <small class="text-muted">...</small>
            </div>
        </div>
        
        <div class="chat-col-body chat-main-body" id="chat-window-body">
            
            {{-- Placeholder state kosong --}}
            <div class="chat-empty-state" id="chat-empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                  <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                </svg>
                <p class="mt-3 fs-5">ROMS Chat Interface</p>
                <p class="text-muted">Pilih chat dari daftar di sebelah kiri untuk memulai percakapan.</p>
            </div>
            
            {{-- Chat bubbles akan di-load di sini oleh JS --}}
            
        </div>
        
        <div class="chat-col-footer" id="chat-reply-section" style="display: none;">
            {{-- 
              Form ini akan di-handle oleh JS (AJAX).
              Action URL (route 'chat.store') akan diset oleh JS.
            --}}
            <form action="#" method="POST" id="chat-reply-form">
                @csrf
                <div class="input-group align-items-center">
                    <input type="text" name="message_body" class="form-control" 
                           placeholder="Ketik balasan..." required
                           autocomplete="off">
                    <button class="btn btn-send ms-2" type="submit" id="button-send">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-fill" viewBox="0 0 16 16">
                            <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083l6-15Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 col-md-3 chat-col chat-detail-col">
        <div class="chat-col-header d-flex align-items-center">
            <h6 class="mb-0">Detail Pelanggan</h6>
        </div>
        
        <div class="chat-col-body chat-detail-body" id="chat-detail-panel">
            {{-- Konten di-load JS --}}
            <img src="https://ui-avatars.com/api/?name=?&background=e0e0e0&color=888&size=120" alt="Avatar Pelanggan" id="detail-img">
            
            <h5 class="mb-1" id="detail-name">{{ $customer->name ?? 'Nama Pelanggan' }}</h5>
            <p class="text-muted mb-3" id="detail-phone">{{ $customer->phone ?? '08xxxx' }}</p>
            
            <ul class="list-group list-group-flush text-start small">
                <li class="list-group-item bg-transparent">
                    <strong>Email:</strong> <span id="detail-email">...</span>
                </li>
                <li class="list-group-item bg-transparent">
                    <strong>Kota:</strong> <span id="detail-city">...</span>
                </li>
                <li class="list-group-item bg-transparent">
                    <strong>Segmen:</strong> <span class="badge bg-secondary" id="detail-segment">...</span>
                </li>
                <li class="list-group-item bg-transparent">
                    <strong>Pesanan Terakhir:</strong>
                    <p class="mb-0 text-muted" id="detail-order">...</p>
                </li>
            </ul>
            
            <hr>
            <div class="d-grid gap-2">
                <a href="#" class="btn btn-outline-primary btn-sm" id="detail-btn-profile">Lihat Detail Pelanggan</a>
                <button class="btn btn-outline-success btn-sm" id="detail-btn-done">Tandai Selesai</button>
            </div>
        </div>
    </div>

</div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // ... (semua deklarasi variabel const Anda masih sama) ...
        const chatListPanel = document.getElementById('chat-list-panel');
        const chatWindowBody = document.getElementById('chat-window-body');
        const chatEmptyState = document.getElementById('chat-empty-state');
        const chatMainHeaderImg = document.getElementById('chat-main-header').querySelector('img');
        const chatMainHeaderName = document.getElementById('chat-main-header').querySelector('h6');
        const chatMainHeaderStatus = document.getElementById('chat-main-header').querySelector('small');
        const detailImg = document.getElementById('detail-img');
        const detailName = document.getElementById('detail-name');
        const detailPhone = document.getElementById('detail-phone');
        const detailEmail = document.getElementById('detail-email');
        const detailCity = document.getElementById('detail-city');
        const detailSegment = document.getElementById('detail-segment');
        const detailOrder = document.getElementById('detail-order');
        const detailBtnProfile = document.getElementById('detail-btn-profile');
        const detailBtnDone = document.getElementById('detail-btn-done');
        const chatReplySection = document.getElementById('chat-reply-section');
        const chatReplyForm = document.getElementById('chat-reply-form');
        const chatReplyInput = chatReplyForm.querySelector('textarea[name="message_body"]');

        let currentRoomId = null; // Kita tetap pakai ini

        // =============================================
        // 1. EVENT LISTENER SAAT CHAT DIKLIK
        // =============================================
        chatListPanel.addEventListener('click', function(e) {
            const chatItem = e.target.closest('.chat-list-item');
            if (chatItem) {
                e.preventDefault();
                
                document.querySelectorAll('.chat-list-item.active').forEach(item => {
                    item.classList.remove('active');
                });
                chatItem.classList.add('active');

                const roomId = chatItem.dataset.roomId;
                
                // [MODIFIKASI] Cek jika room-nya berbeda
                if (currentRoomId !== roomId) {
                    // [BARU] Tinggalkan channel lama sebelum join yang baru
                    leaveChannel(currentRoomId); 
                    
                    currentRoomId = roomId;
                    loadRoomData(roomId);
                }
            }
        });

        // =============================================
        // 2. FUNGSI UNTUK LOAD DATA (AJAX/FETCH)
        // =============================================
        async function loadRoomData(roomId) {
            chatWindowBody.innerHTML = '<p class="text-center p-5 text-muted">Memuat percakapan...</p>';

            try {
                const response = await fetch(`/app/chat/room/${roomId}/data`);
                if (!response.ok) {
                    throw new Error('Gagal memuat data chat. Status: ' + response.status);
                }
                const data = await response.json();
                
                if (chatEmptyState) {
                    chatEmptyState.style.display = 'none';
                }

                renderChatHeader(data.room.customer);
                renderCustomerDetails(data.room.customer);
                renderMessages(data.messages);
                setupSendForm(data.room.id);
                
                // [BARU] Setelah data di-load, mulai dengarkan channel
                listenForMessages(roomId);
                
                scrollToBottom();

            } catch (error) {
                console.error(error);
                chatWindowBody.innerHTML = `<p class="text-center p-5 text-danger">${error.message}</p>`;
            }
        }

        // =============================================
        // 3. [BARU] FUNGSI UNTUK WEBSOCKET (ECHO)
        // =============================================

        /**
         * [BARU] Mulai mendengarkan di channel privat untuk room_id tertentu.
         */
        function listenForMessages(roomId) {
            // Kita join channel 'chat-room.1' (sesuai 'broadcastOn' di Event PHP)
            window.Echo.private(`chat-room.${roomId}`)
                .listen('.new-message', (e) => { 
                    // e.message adalah payload dari event NewChatMessage
                    
                    console.log('Pesan baru diterima:', e.message);

                    // Kita hanya ingin menampilkan pesan masuk dari 'customer'
                    // Pesan dari 'user' (CS) sudah di-handle oleh form submit
                    // Pengecekan ini juga mencegah pesan 'toOthers()' dari CS lain
                    // muncul jika tidak di-handle dengan benar.
                    
                    // Kita cek: jika yang sedang aktif adalah room ini
                    if (e.message.chat_room_id == currentRoomId) {
                        appendMessage(e.message);
                        scrollToBottom();
                    }

                    // TODO: Tampilkan notifikasi 'badge' di list kiri
                    // jika e.message.chat_room_id BUKAN currentRoomId
                });
            
            console.log(`Mendengarkan di channel: chat-room.${roomId}`);
        }

        /**
         * [BARU] Berhenti mendengarkan di channel.
         */
        function leaveChannel(roomId) {
            if (roomId) {
                window.Echo.leave(`chat-room.${roomId}`);
                console.log(`Meninggalkan channel: chat-room.${roomId}`);
            }
        }


        // =============================================
        // 4. FUNGSI-FUNGSI RENDER (Tidak berubah)
        // =============================================
        function renderChatHeader(customer) {
            chatMainHeaderImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(customer.name)}&background=random`;
            chatMainHeaderName.textContent = customer.name;
            chatMainHeaderStatus.textContent = customer.phone;
        }

        function renderCustomerDetails(customer) {
            detailImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(customer.name)}&background=e0e0e0&color=888&size=120`;
            detailName.textContent = customer.name;
            detailPhone.textContent = customer.phone;
            detailEmail.textContent = customer.email || '...';
            detailCity.textContent = customer.city || '...';
            detailSegment.textContent = customer.segment_tag || '...';
            detailOrder.textContent = 'Belum ada data pesanan terakhir.';
        }

        function renderMessages(messages) {
            chatWindowBody.innerHTML = ''; 
            if (messages.length === 0) {
                chatWindowBody.innerHTML = '<p class="text-center p-5 text-muted">Belum ada pesan. Mulai percakapan!</p>';
                return;
            }
            messages.forEach(message => {
                appendMessage(message);
            });
        }
        
        function appendMessage(message) {
            // Hapus 'empty state' jika ada saat pesan pertama masuk
            if (chatEmptyState) {
                chatEmptyState.style.display = 'none';
            }
            
            const bubble = document.createElement('div');
            bubble.classList.add('chat-bubble');
            
            if (message.sender_type === 'customer') {
                bubble.classList.add('customer');
            } else {
                bubble.classList.add('cs');
            }
            
            const timestamp = new Date(message.created_at).toLocaleTimeString('id-ID', {
                hour: '2-digit', 
                minute: '2-digit'
            });

            bubble.innerHTML = `
                ${escapeHTML(message.message_content)}
                <div class="timestamp ${message.sender_type === 'customer' ? 'text-start' : 'text-end'}">
                    ${timestamp}
                </div>
            `;
            chatWindowBody.appendChild(bubble);
        }

        // =============================================
        // 5. FUNGSI FORM KIRIM PESAN (Tidak berubah)
        // =============================================
        function setupSendForm(roomId) {
            chatReplyForm.action = `/app/chat/room/${roomId}/send-ajax`;
            chatReplySection.style.display = 'block';
        }

        chatReplyForm.addEventListener('submit', async function(e) {
            e.preventDefault(); 
            
            const url = chatReplyForm.action;
            const messageBody = chatReplyInput.value;
            const csrfToken = chatReplyForm.querySelector('input[name="_token"]').value;

            if (!messageBody.trim()) return;

            // [MODIFIKASI] Nonaktifkan tombol saat mengirim
            const sendButton = chatReplyForm.querySelector('button');
            sendButton.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message_body: messageBody
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Kita panggil appendMessage agar konsisten
                    // Event 'toOthers()' akan mencegah ini duplikat
                    appendMessage(data.message);
                    chatReplyInput.value = '';
                    scrollToBottom();
                } else {
                    alert('Error: ' + data.error);
                }

            } catch (error) {
                console.error('Gagal mengirim pesan:', error);
                alert('Terjadi kesalahan. Lihat konsol.');
            } finally {
                // [MODIFIKASI] Aktifkan kembali tombol
                sendButton.disabled = false;
                chatReplyInput.focus();
            }
        });

        // =============================================
        // 6. FUNGSI UTILITAS (Tidak berubah)
        // =============================================
        function scrollToBottom() {
            chatWindowBody.scrollTop = chatWindowBody.scrollHeight;
        }
        
        function escapeHTML(str) {
            // ... (fungsi escapeHTML) ...
            return str.replace(/[&<>"']/g, function(m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[m];
            });
        }

    });
</script>