@extends('layout.main')

@section('title', 'Chat Dashboard - ROMS')

@push('styles')
<style>
    /* --- LAYOUT --- */
    /* --- LAYOUT --- */
    /* Override Main Content Padding for this page only */
    .main-content {
        padding: 0 !important;
        margin-top: var(--topbar-height);
        height: calc(100vh - var(--topbar-height));
        overflow: hidden; /* Disable outer scroll */
        display: flex;
        flex-direction: column;
    }

    /* Ensure container fills the height */
    .main-content > .container-fluid {
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .chat-dashboard {
        flex: 1; /* Fill the container */
        display: flex;
        overflow: hidden;
        background: #fff;
        box-shadow: none;
        min-height: 0; /* Important for nested flex scrolling */
    }

    /* --- COL 1: LIST --- */
    .col-list {
        width: 320px;
        border-right: 1px solid #e9ecef;
        display: flex;
        flex-direction: column;
        background: #fff;
    }
    .list-header {
        padding: 15px;
        border-bottom: 1px solid #f0f2f5;
    }
    .list-body {
        flex: 1;
        overflow-y: auto;
    }
    .chat-item {
        padding: 15px;
        border-bottom: 1px solid #f8f9fa;
        cursor: pointer;
        transition: background 0.2s;
    }
    .chat-item:hover, .chat-item.active {
        background: #f0f2f5;
    }
    .chat-item.unread {
        background: #e7f3ff;
    }

    /* --- COL 2: CHAT WINDOW --- */
    .col-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #efe7dd; /* WA BG */
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        position: relative;
    }
    .chat-header {
        background: #f0f2f5;
        padding: 10px 20px;
        border-bottom: 1px solid #d1d7db;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 60px;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }
    .chat-input-area {
        background: #f0f2f5;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* --- COL 3: INFO --- */
    .col-info {
        width: 300px;
        border-left: 1px solid #e9ecef;
        background: #fff;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }
    .info-header {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid #f0f2f5;
    }
    .info-section {
        padding: 15px;
        border-bottom: 1px solid #f8f9fa;
    }
    .info-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* --- COMPONENTS --- */
    .avatar {
        width: 45px; height: 45px;
        border-radius: 50%;
        background: #0d6efd;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
    }
    .msg-bubble {
        max-width: 75%;
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        position: relative;
        font-size: 14px;
        line-height: 1.4;
        box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: break-word;
    }
    .msg-bubble.in { background: #fff; align-self: flex-start; border-top-left-radius: 0; }
    .msg-bubble.out { background: #d9fdd3; align-self: flex-end; border-top-right-radius: 0; }
    
    .order-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    .order-status {
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 4px;
    }
</style>
@endpush

@section('main-content')
<div class="container-fluid p-0">
    <div class="chat-dashboard">
        
        {{-- COL 1: LIST --}}
        <div class="col-list">
            <div class="list-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold">Chat</h5>
                    <button class="btn btn-sm btn-primary rounded-circle"><i class="bi bi-plus-lg"></i></button>
                </div>
                <input type="text" class="form-control form-control-sm" placeholder="Cari percakapan...">
            </div>
            <div class="list-body" id="chat-list">
                @foreach($rooms as $room)
                <div class="chat-item {{ $room->status == 'new' ? 'unread' : '' }}" onclick="loadChat({{ $room->id }}, this)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-primary">
                            {{ strtoupper(substr($room->customer->name ?? 'G', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0 text-truncate">{{ $room->customer->name ?? 'Guest' }}</h6>
                                <small class="text-muted" style="font-size: 11px;">{{ $room->updated_at->format('H:i') }}</small>
                            </div>
                            <small class="text-muted text-truncate d-block">
                                {{ $room->customer->phone }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- COL 2: CHAT WINDOW --}}
        <div class="col-chat">
            {{-- EMPTY STATE --}}
            <div id="chat-empty-state" class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-4">
                <div class="bg-white p-4 rounded-circle shadow-sm mb-3">
                    <i class="bi bi-whatsapp fs-1 text-success"></i>
                </div>
                <h4>Selamat Datang di ROMS Chat</h4>
                <p class="text-muted">Pilih percakapan dari daftar sebelah kiri untuk mulai chatting.</p>
            </div>

            {{-- ACTIVE CHAT CONTENT (Hidden by default) --}}
            <div id="chat-content" class="d-none flex-column h-100">
                <div class="chat-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-secondary" id="header-avatar">A</div>
                        <div>
                            <h6 class="mb-0 fw-bold" id="header-name">Nama Customer</h6>
                            <small class="text-muted" id="header-status">Online</small>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-light btn-sm"><i class="bi bi-search"></i></button>
                        <button class="btn btn-light btn-sm"><i class="bi bi-three-dots-vertical"></i></button>
                    </div>
                </div>

                <div class="chat-messages" id="messages-container">
                    {{-- Messages injected via JS --}}
                </div>

                <div class="chat-input-area position-relative">
                    <!-- Emoji Picker Container (Absolute) -->
                    <div id="emoji-picker-container" class="position-absolute bottom-100 start-0 mb-2 ms-3 d-none" style="z-index: 1000;">
                        <emoji-picker></emoji-picker>
                    </div>

                    <!-- File Preview Container (Absolute) -->
                    <div id="file-preview-container" class="position-absolute bottom-100 start-0 mb-2 ms-3 p-2 bg-light rounded border d-none" style="z-index: 1000; min-width: 200px;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                            <span id="file-preview-name" class="small text-truncate" style="max-width: 150px;">filename.jpg</span>
                            <button type="button" class="btn-close small ms-auto" onclick="clearFile()"></button>
                        </div>
                    </div>

                    <button class="btn btn-light rounded-circle" onclick="toggleEmojiPicker()"><i class="bi bi-emoji-smile"></i></button>
                    <button class="btn btn-light rounded-circle" onclick="document.getElementById('file-input').click()"><i class="bi bi-paperclip"></i></button>
                    
                    <input type="file" id="file-input" class="d-none" onchange="handleFileSelect(this)">
                    <input type="text" id="message-input" class="form-control border-0" placeholder="Ketik pesan..." onkeypress="handleEnter(event)">
                    <button class="btn btn-primary rounded-circle" onclick="sendMessage()"><i class="bi bi-send-fill"></i></button>
                </div>
            </div>
        </div>

        {{-- COL 3: INFO --}}
        <div class="col-info">
            <div id="info-empty-state" class="text-center p-5 text-muted">
                <i class="bi bi-person-badge fs-1"></i>
                <p class="mt-2">Info Customer</p>
            </div>

            <div id="info-content" class="d-none">
                <div class="info-header">
                    <div class="avatar bg-primary mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;" id="info-avatar">
                        A
                    </div>
                    <h5 class="fw-bold mb-1" id="info-name">Nama Customer</h5>
                    <p class="text-muted mb-0" id="info-phone">+62 812 3456 7890</p>
                </div>

                <div class="info-section">
                    <div class="info-title">Status Customer</div>
                    <div id="info-badges">
                        <span class="badge bg-secondary">Loading...</span>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-title d-flex justify-content-between align-items-center">
                        Riwayat Pesanan
                        <a href="#" class="text-decoration-none" style="font-size: 0.75rem;">Lihat Semua</a>
                    </div>
                    <div id="order-history-list">
                        {{-- Orders injected via JS --}}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Hidden Inputs --}}
<input type="hidden" id="current-room-id" value="">
<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

@endsection

@push('scripts')
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
<script>
    let currentRoomId = null;

    // --- 1. LOAD CHAT ---
    function loadChat(roomId, element) {
        // UI Active State
        document.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        // Stop previous polling if any (logic handled by currentRoomId check)
        currentRoomId = roomId;
        lastMessageId = 0; // Reset last message ID
        isPollingChat = false; // Reset polling flag to allow new poll to start

        // Fetch Data (Initial Load)
        fetch(`/app/chat/room/${roomId}/data`)
            .then(response => response.json())
            .then(data => {
                renderChatUI(data);
                // Start Polling for this room
                pollActiveChat();
            })
            .catch(err => console.error('Error loading chat:', err));
    }

    function renderChatUI(data) {
        const room = data.room;
        const messages = data.messages;
        const orders = data.orders;
        const customer = room.customer;

        currentRoomId = room.id;
        document.getElementById('current-room-id').value = room.id;

        // --- RENDER MIDDLE COL (CHAT) ---
        document.getElementById('chat-empty-state').classList.add('d-none');
        document.getElementById('chat-content').classList.remove('d-none');
        document.getElementById('chat-content').classList.add('d-flex');

        // Header
        document.getElementById('header-name').innerText = customer.name;
        document.getElementById('header-avatar').innerText = customer.name.charAt(0).toUpperCase();
        
        // Messages
        const msgContainer = document.getElementById('messages-container');
        msgContainer.innerHTML = ''; // Clear old messages

        let lastDate = null;

        messages.forEach(msg => {
            // Date Separator Logic
            const msgDate = new Date(msg.created_at).toDateString();
            if (msgDate !== lastDate) {
                const separatorHtml = `
                    <div class="date-separator text-center text-muted my-3" style="font-size: 0.8rem;">
                        <span class="bg-light px-2 py-1 rounded border">${formatDateSeparator(msg.created_at)}</span>
                    </div>`;
                msgContainer.insertAdjacentHTML('beforeend', separatorHtml);
                lastDate = msgDate;
            }
            // Update global lastRenderedDate saat initial load
            window.lastRenderedDate = lastDate;

            const isMe = msg.sender_type != 'customer';
            const bubbleClass = isMe ? 'out' : 'in';
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            let statusIcon = '';
            if (isMe) {
                 statusIcon = '<i class="bi bi-check2"></i>';
                 if (msg.status === 'sent') statusIcon = '<i class="bi bi-check2-all text-secondary"></i>';
                 if (msg.status === 'read') statusIcon = '<i class="bi bi-check2-all text-primary"></i>';
            }

            let attachmentHtml = '';
            if (msg.attachment_url) {
                if (msg.attachment_type === 'image') {
                    attachmentHtml = `<div class="mb-2"><img src="${msg.attachment_url}" class="img-fluid rounded" style="max-width: 200px; max-height: 200px; object-fit: cover;"></div>`;
                } else {
                    attachmentHtml = `<div class="mb-2"><a href="${msg.attachment_url}" target="_blank" class="text-decoration-none text-reset p-2 bg-light rounded d-flex align-items-center gap-2"><i class="bi bi-file-earmark-text fs-4"></i> <span class="small">Attachment</span></a></div>`;
                }
            }

            const html = `
                <div class="msg-bubble ${bubbleClass}">
                    ${attachmentHtml}
                    ${msg.message_content || ''}
                    <div class="text-end text-muted" style="font-size: 10px; margin-top: 4px;">${time} ${statusIcon}</div>
                </div>
            `;
            msgContainer.insertAdjacentHTML('beforeend', html);
        });
        
        // Scroll to bottom
        msgContainer.scrollTop = msgContainer.scrollHeight;

        // --- RENDER RIGHT COL (INFO) ---
        document.getElementById('info-empty-state').classList.add('d-none');
        document.getElementById('info-content').classList.remove('d-none');

        document.getElementById('info-name').innerText = customer.name;
        document.getElementById('info-phone').innerText = customer.phone;
        document.getElementById('info-avatar').innerText = customer.name.charAt(0).toUpperCase();

        // Status Badges
        const badgeContainer = document.getElementById('info-badges');
        if (badgeContainer) {
            badgeContainer.innerHTML = ''; 
            const segment = customer.segment || 'Regular';
            let badgeClass = 'bg-secondary';
            
            if (segment === 'Loyal') badgeClass = 'bg-info text-dark';
            if (segment === 'Big Spender') badgeClass = 'bg-warning text-dark';
            if (segment === 'New Member') badgeClass = 'bg-success';
            if (segment === 'Inactive') badgeClass = 'bg-danger';

            badgeContainer.innerHTML = `<span class="badge ${badgeClass}">${segment}</span>`;
        }

        // Orders
        const orderList = document.getElementById('order-history-list');
        orderList.innerHTML = '';

        if (orders.length === 0) {
            orderList.innerHTML = '<p class="text-muted small text-center my-3">Belum ada pesanan.</p>';
        } else {
            orders.forEach(order => {
                const statusColor = order.status === 'completed' ? 'success' : (order.status === 'pending' ? 'warning' : 'secondary');
                const html = `
                    <div class="order-card">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold text-primary">${order.order_number}</span>
                            <span class="order-status bg-${statusColor} text-white">${order.status}</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>${order.date}</span>
                            <span>Rp ${order.total_amount}</span>
                        </div>
                    </div>
                `;
                orderList.insertAdjacentHTML('beforeend', html);
            });
        }
    }

    // --- 2. SEND MESSAGE & EMOJI & FILE ---

    // Emoji Logic
    function toggleEmojiPicker() {
        const picker = document.getElementById('emoji-picker-container');
        if (picker) picker.classList.toggle('d-none');
    }

    // Event Listener for Emoji Click
    document.addEventListener('DOMContentLoaded', () => {
        const picker = document.querySelector('emoji-picker');
        if (picker) {
            picker.addEventListener('emoji-click', event => {
                const input = document.getElementById('message-input');
                if (input) {
                    input.value += event.detail.unicode;
                    input.focus();
                    // Opsional: toggleEmojiPicker(); // Jika ingin menutup picker setelah pilih
                }
            });
        }
    });

    // File Logic
    let selectedFile = null;

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            selectedFile = input.files[0];
            const previewContainer = document.getElementById('file-preview-container');
            const previewName = document.getElementById('file-preview-name');
            if (previewContainer && previewName) {
                previewContainer.classList.remove('d-none');
                previewName.innerText = selectedFile.name;
            }
        }
    }

    function clearFile() {
        selectedFile = null;
        const fileInput = document.getElementById('file-input');
        if (fileInput) fileInput.value = '';
        const previewContainer = document.getElementById('file-preview-container');
        if (previewContainer) previewContainer.classList.add('d-none');
    }

    function handleEnter(e) {
        if (e.key === 'Enter') sendMessage();
    }

    function sendMessage() {
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        
        if ((!message && !selectedFile) || !currentRoomId) return;

        // Optimistic UI Update
        const msgContainer = document.getElementById('messages-container');
        const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false});
        
        let contentHtml = message;
        if (selectedFile) {
            contentHtml += `<div class="mt-1 small text-muted"><i class="bi bi-paperclip"></i> ${selectedFile.name} (Uploading...)</div>`;
        }

        const html = `
            <div class="msg-bubble out opacity-50">
                ${contentHtml}
                <div class="text-end text-muted" style="font-size: 10px; margin-top: 4px;">${time} <i class="bi bi-clock"></i></div>
            </div>
        `;
        msgContainer.insertAdjacentHTML('beforeend', html);
        msgContainer.scrollTop = msgContainer.scrollHeight;
        
        // Prepare Data
        const formData = new FormData();
        formData.append('message_body', message); 
        if (selectedFile) {
            formData.append('attachment', selectedFile);
        }

        // Reset Input
        input.value = '';
        clearFile();
        const picker = document.getElementById('emoji-picker-container');
        if (picker && !picker.classList.contains('d-none')) toggleEmojiPicker();

        // Send AJAX
        fetch(`/app/chat/room/${currentRoomId}/send-ajax`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.getElementById('csrf-token').value
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const lastBubble = msgContainer.lastElementChild;
                lastBubble.classList.remove('opacity-50');
                lastBubble.querySelector('.bi-clock').className = 'bi bi-check2';
            } else {
                alert('Gagal mengirim pesan: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => console.error('Error sending message:', err));
    }
    // --- HELPER FUNCTIONS FOR DATE ---
    function formatChatListTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const isToday = date.toDateString() === now.toDateString();
        
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        const isYesterday = date.toDateString() === yesterday.toDateString();

        if (isToday) {
            return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false});
        } else if (isYesterday) {
            return 'Kemarin';
        } else {
            return date.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: '2-digit'}); // 28/11/24
        }
    }

    function formatDateSeparator(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const isToday = date.toDateString() === now.toDateString();
        
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        const isYesterday = date.toDateString() === yesterday.toDateString();

        if (isToday) {
            return 'Hari Ini';
        } else if (isYesterday) {
            return 'Kemarin';
        } else {
            return date.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}); // 28 November 2024
        }
    }

    // --- 3. REAL-TIME LONG POLLING (List & Chat) ---
    
    let lastListUpdate = 0;
    let lastGlobalMsgId = 0; // [NEW] Untuk deteksi pesan baru lebih akurat
    let lastMessageId = 0;
    let isPollingList = false;
    let isPollingChat = false;

    // Start Polling
    pollChatList();

    // A. Polling List Chat (Short Polling / Periodic)
    function pollChatList() {
        if (isPollingList) return;
        isPollingList = true;

        fetch(`/app/chat/rooms?last_updated_at=${lastListUpdate}&last_global_msg_id=${lastGlobalMsgId}`)
            .then(response => response.json())
            .then(data => {
                // Jika status no_update, berarti tidak ada perubahan
                if (data.status === 'no_update') {
                    isPollingList = false;
                    setTimeout(pollChatList, 2000); // Cek lagi 2 detik kemudian
                    return;
                }

                if (data.rooms) {
                    lastListUpdate = data.last_updated_at;
                    lastGlobalMsgId = data.last_global_msg_id || lastGlobalMsgId; // Update ID global
                    const rooms = data.rooms;
                    const listBody = document.getElementById('chat-list');
                    
                    let html = '';
                    rooms.forEach(room => {
                        const isActive = (currentRoomId == room.id) ? 'active' : '';
                        const unreadClass = (room.status == 'new') ? 'unread' : '';
                        const latestMsgObj = room.latest_message;
                        const displayTime = latestMsgObj ? latestMsgObj.created_at : room.updated_at;
                        const time = formatChatListTime(displayTime);
                        const customerName = room.customer.name;
                        const latestMsg = latestMsgObj ? latestMsgObj.message_content : room.customer.phone;

                        html += `
                            <div class="chat-item ${isActive} ${unreadClass}" onclick="loadChat(${room.id}, this)">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar bg-primary">
                                        ${customerName.charAt(0).toUpperCase()}
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-0 text-truncate">${customerName}</h6>
                                            <small class="text-muted" style="font-size: 11px;">${time}</small>
                                        </div>
                                        <small class="text-muted text-truncate d-block">
                                            ${latestMsg}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    listBody.innerHTML = html;
                }

                // Lanjut polling lagi setelah delay
                isPollingList = false;
                setTimeout(pollChatList, 2000);
            })
            .catch(err => {
                console.error('Polling List Error:', err);
                isPollingList = false;
                setTimeout(pollChatList, 5000); // Jika error, tunggu lebih lama
            });
    }

    // B. Polling Active Chat (Short Polling / Periodic)
    function pollActiveChat() {
        if (!currentRoomId || isPollingChat) return;
        isPollingChat = true;

        fetch(`/app/chat/room/${currentRoomId}/data?polling=1&last_message_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'no_update') {
                    isPollingChat = false;
                    if (currentRoomId) setTimeout(pollActiveChat, 2000);
                    return;
                }

                if (data.messages) {
                    lastMessageId = data.last_message_id;
                    const messages = data.messages;
                    const msgContainer = document.getElementById('messages-container');
                    
                    const currentScroll = msgContainer.scrollTop;
                    const isAtBottom = (msgContainer.scrollHeight - msgContainer.scrollTop) <= (msgContainer.clientHeight + 50);

                    let html = '';
                    let lastDate = null;
                    // Ambil tanggal terakhir dari pesan yang sudah ada di DOM (jika ada) untuk separator
                    // Namun karena kita append, kita perlu tahu tanggal pesan terakhir di UI.
                    // Simplifikasi: Kita cek tanggal pesan pertama di batch baru vs tanggal pesan terakhir di UI.
                    // Tapi karena short polling fetch *semua* pesan (atau partial?), di sini fetch logicnya:
                    // /app/chat/room/{id}/messages?last_id={lastMessageId}
                    // Jadi kita hanya dapat pesan BARU.
                    
                    // Cek tanggal pesan terakhir di UI
                    const lastMsgElement = msgContainer.lastElementChild;
                    // Kita tidak simpan tanggal di elemen, jadi agak tricky.
                    // Workaround: Kita anggap pesan baru selalu butuh cek separator vs pesan sebelumnya.
                    // Tapi kita tidak punya data tanggal pesan sebelumnya di variabel JS.
                    // Solusi: Kita cek apakah pesan baru beda hari dengan "Hari Ini" (karena polling jalan realtime).
                    // ATAU: Kita simpan lastDate di variabel global saat loadChat.
                    
                    // Untuk simplifikasi polling:
                    // Jika pesan baru masuk realtime, kemungkinan besar hari ini.
                    // Kecuali pesan pending dari kemarin baru masuk (jarang).
                    // Kita pakai logika sederhana: Jika pesan pertama di batch ini beda tanggal dengan pesan terakhir di UI...
                    // Tapi susah akses DOM date.
                    
                    // Revisi: Kita render separator untuk pesan baru jika beda hari dengan pesan sebelumnya DI BATCH INI.
                    // Dan untuk pesan pertama di batch, kita bandingkan dengan... well, anggap saja "Hari Ini" jika realtime.
                    // Atau kita cek lastMessageId punya tanggal berapa? Tidak punya datanya.
                    
                    // Opsi Aman: Render separator jika tanggal pesan != tanggal hari ini (asumsi chat aktif hari ini).
                    // Atau: Kita biarkan separator hanya muncul saat load full chat (renderChatUI).
                    // Saat polling, pesan baru biasanya "Hari Ini".
                    // Jika ganti hari saat polling (tengah malam), kita perlu separator "Hari Ini".
                    
                    // Implementasi:
                    // Kita simpan lastMessageDate global.
                    
                    messages.forEach(msg => {
                        // Date Separator Logic (Simplified for Polling)
                        // Kita butuh state lastMessageDate global untuk akurasi antar-polling.
                        // Tapi untuk sekarang, kita insert separator jika msgDate != lastMessageDate (global).
                        
                        const msgDate = new Date(msg.created_at).toDateString();
                        // Kita perlu akses lastRenderedDate dari scope luar atau DOM.
                        // Hack: Kita cek apakah ada separator "Hari Ini" di paling bawah?
                        // Better: Kita tambahkan separator jika msgDate != lastRenderedDate.
                        
                        // Mari kita definisikan lastRenderedDate di scope global chat.js/script
                        if (typeof window.lastRenderedDate === 'undefined') window.lastRenderedDate = null;
                        
                        if (msgDate !== window.lastRenderedDate) {
                             html += `<div class="date-separator text-center text-muted my-3" style="font-size: 0.8rem;">
                                        <span class="bg-light px-2 py-1 rounded border">${formatDateSeparator(msg.created_at)}</span>
                                     </div>`;
                             window.lastRenderedDate = msgDate;
                        }

                        const isMe = msg.sender_type != 'customer';
                        const bubbleClass = isMe ? 'out' : 'in';
                        const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false});
                        
                        let statusIcon = '';
                        if (isMe) {
                             statusIcon = '<i class="bi bi-check2"></i>';
                             if (msg.status === 'sent') statusIcon = '<i class="bi bi-check2-all text-secondary"></i>';
                             if (msg.status === 'read') statusIcon = '<i class="bi bi-check2-all text-primary"></i>';
                        }

                        let attachmentHtml = '';
                        if (msg.attachment_url) {
                            if (msg.attachment_type === 'image') {
                                attachmentHtml = `<div class="mb-2"><img src="${msg.attachment_url}" class="img-fluid rounded" style="max-width: 200px; max-height: 200px; object-fit: cover;"></div>`;
                            } else {
                                attachmentHtml = `<div class="mb-2"><a href="${msg.attachment_url}" target="_blank" class="text-decoration-none text-reset p-2 bg-light rounded d-flex align-items-center gap-2"><i class="bi bi-file-earmark-text fs-4"></i> <span class="small">Attachment</span></a></div>`;
                            }
                        }

                        html += `
                            <div class="msg-bubble ${bubbleClass}">
                                ${attachmentHtml}
                                ${msg.message_content || ''}
                                <div class="text-end text-muted" style="font-size: 10px; margin-top: 4px;">
                                    ${time} ${statusIcon}
                                </div>
                            </div>
                        `;
                    });

                    if (msgContainer.innerHTML.length !== html.length) {
                         msgContainer.innerHTML = html;
                         if (isAtBottom) {
                             msgContainer.scrollTop = msgContainer.scrollHeight;
                         } else {
                             msgContainer.scrollTop = currentScroll;
                         }
                    }
                }

                isPollingChat = false;
                if (currentRoomId) setTimeout(pollActiveChat, 2000);
            })
            .catch(err => {
                console.error('Polling Chat Error:', err);
                isPollingChat = false;
                if (currentRoomId) setTimeout(pollActiveChat, 5000);
            });
    }
</script>
@endpush
