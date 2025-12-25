@extends('layouts.app')

@section('title', 'Chat WhatsApp')

@section('content')

{{-- Hidden CSRF Token --}}
<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

{{-- Custom CSS for Chat Layout Fix --}}
<style>
    /* Main container must have defined height */
    .chat-main-container {
        height: calc(100vh - 140px);
        display: flex;
        overflow: hidden;
    }
    
    #empty-state {
        display: flex;
        flex: 1;
        align-items: center;
        justify-content: center;
    }
    
    #active-chat {
        display: none;
        height: 100%;
        flex-direction: column;
    }
    
    #active-chat.show {
        display: flex !important;
    }
    
    #messages-container {
        flex: 1 1 0%;
        overflow-y: auto;
        min-height: 0;
    }
    
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Loading overlay */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(229, 221, 213, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
</style>

{{-- FULL SCREEN CHAT LAYOUT (3 Columns: List | Chat | Info) --}}
<div class="chat-main-container bg-slate-50 rounded-xl border border-slate-200 shadow-lg">
    
    {{-- COLUMN 1: CHAT LIST (Left Sidebar) --}}
    <div class="w-full md:w-[380px] bg-white border-r border-slate-200 flex flex-col">
        {{-- Header --}}
        <div class="px-5 py-4 border-b border-slate-200 flex-shrink-0">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="message-circle" class="h-5 w-5 text-[#84994F]"></i>
                Pesan
            </h3>
        </div>
        
        {{-- Search --}}
        <div class="px-4 py-3 border-b border-slate-50 flex-shrink-0">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                <input type="text" id="search-chat" placeholder="Cari kontak..." 
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#84994F]/30 focus:border-[#84994F] transition">
            </div>
        </div>
        
        {{-- Chat List --}}
        <div id="chat-list" class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-center h-full text-slate-400">
                <div class="text-center">
                    <i data-lucide="loader" class="h-8 w-8 mx-auto mb-2 animate-spin"></i>
                    <p class="text-sm">Memuat percakapan...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- COLUMN 2: CHAT WINDOW (Center) --}}
    <div id="chat-window" class="flex-1 flex flex-col bg-[#e5ddd5] relative" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');">
        
        {{-- Default Empty State --}}
        <div id="empty-state">
            <div class="text-center text-slate-400">
                <div class="mb-4 inline-block p-6 bg-white/50 rounded-full">
                    <i data-lucide="message-square" class="h-16 w-16"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-600 mb-2">WhatsApp Chat</h4>
                <p class="text-sm">Pilih percakapan untuk mulai chat</p>
            </div>
        </div>

        {{-- Active Chat Container --}}
        <div id="active-chat">
            {{-- Loading Overlay --}}
            <div id="chat-loading" class="loading-overlay hidden">
                <div class="text-center">
                    <i data-lucide="loader" class="h-10 w-10 mx-auto mb-3 animate-spin text-[#84994F]"></i>
                    <p class="text-sm text-slate-600 font-medium">Memuat pesan...</p>
                </div>
            </div>
            
            {{-- Chat Header --}}
            <div class="bg-[#f0f2f5] border-b border-slate-300 px-5 py-3 flex justify-between items-center flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div id="chat-avatar" class="w-10 h-10 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold text-lg">
                    </div>
                    <div>
                        <h6 id="chat-name" class="font-bold text-slate-800 text-base"></h6>
                        <div class="flex items-center gap-2">
                            <small id="chat-phone" class="text-slate-500 text-xs"></small>
                            <span id="cs-badge" class="hidden text-[10px] px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full font-bold border border-blue-200">
                                <i data-lucide="user-check" class="h-3 w-3 inline"></i>
                                <span id="cs-name-badge"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if(auth()->user()->role === 'admin')
                    <button id="btn-reassign-cs" onclick="openReassignModal()" class="hidden text-slate-600 hover:bg-slate-200 p-2 rounded-lg transition" title="Reassign CS">
                        <i data-lucide="user-cog" class="h-5 w-5"></i>
                    </button>
                    @endif
                    {{-- Info button visible for all users (admin and CS) --}}
                    <button onclick="toggleInfoPanel()" class="text-slate-600 hover:bg-slate-200 p-2 rounded-lg transition" title="Info Kontak">
                        <i data-lucide="info" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>

            {{-- Messages Area --}}
            <div id="messages-container" class="flex-1 overflow-y-auto p-5 flex flex-col gap-2 custom-scrollbar">
                {{-- Messages will be inserted here via JS --}}
            </div>

            {{-- File Preview (Hidden) --}}
            <div id="file-preview-container" class="hidden px-5 py-3 bg-slate-100 border-t border-slate-200 flex-shrink-0">
                <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-lg">
                    <i data-lucide="file" class="h-5 w-5 text-slate-400"></i>
                    <span id="file-preview-name" class="flex-1 text-sm text-slate-700"></span>
                    <button onclick="clearFile()" class="text-red-500 hover:text-red-700">
                        <i data-lucide="x" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>

            {{-- Chat Footer (Input) --}}
            <div class="bg-[#f0f2f5] border-t border-slate-300 px-5 py-3 flex items-center gap-3 flex-shrink-0">
                {{-- Emoji Button --}}
                <button onclick="toggleEmojiPicker()" class="text-slate-500 hover:text-slate-700 transition" title="Emoji">
                    <i data-lucide="smile" class="h-6 w-6"></i>
                </button>
                
                {{-- File Attach --}}
                <label for="file-input" class="text-slate-500 hover:text-slate-700 transition cursor-pointer" title="Attach">
                    <i data-lucide="paperclip" class="h-6 w-6"></i>
                </label>
                <input type="file" id="file-input" class="hidden" onchange="handleFileSelect(this)">
                
                {{-- Message Input --}}
                <input type="text" id="message-input" 
                    class="flex-1 border-none rounded-lg px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[#84994F]/30 bg-white" 
                    placeholder="Ketik pesan..." 
                    onkeypress="handleEnter(event)">
                
                {{-- Send Button --}}
                <button onclick="sendMessage()" 
                    class="w-11 h-11 flex items-center justify-center rounded-full bg-[#84994F] hover:bg-[#6b7d3f] text-white transition shadow-md">
                    <i data-lucide="send" class="h-5 w-5"></i>
                </button>
            </div>
        </div>

        {{-- Emoji Picker Container (Hidden) --}}
        <div id="emoji-picker-container" class="hidden absolute bottom-20 left-20 z-50 bg-white rounded-xl shadow-2xl border border-slate-200">
            <emoji-picker style="--emoji-size: 1.5rem;"></emoji-picker>
        </div>
    </div>

    {{-- COLUMN 3: INFO PANEL (Right Sidebar - Toggle) --}}
    <div id="info-panel" class="hidden w-[360px] bg-white border-l border-slate-200 flex-col">
        {{-- Header --}}
        <div class="px-5 py-4 border-b border-slate-200 flex justify-between items-center flex-shrink-0">
            <h4 class="font-bold text-slate-800">Info Kontak</h4>
            <button onclick="toggleInfoPanel()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        {{-- Scrollable Content Area --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            {{-- Customer Info --}}
            <div class="px-5 py-6 border-b border-slate-100">
                <div class="text-center mb-4">
                    <div id="info-avatar" class="w-20 h-20 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold text-2xl mx-auto mb-3"></div>
                    <h5 id="info-name" class="font-bold text-slate-800 text-lg"></h5>
                    <p id="info-phone" class="text-slate-500 text-sm mb-3"></p>
                    
                    {{-- Add/Edit Customer Button --}}
                    <button onclick="openEditCustomerModal()" id="btn-edit-customer" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition flex items-center gap-2 mx-auto">
                        <i data-lucide="user-plus" class="h-4 w-4"></i>
                        <span>Tambahkan sebagai Pelanggan</span>
                    </button>
                </div>
            </div>

            {{-- Customer Details --}}
            <div class="px-5 py-4 border-b border-slate-100">
                <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Detail Pelanggan</h6>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-slate-500">Email:</span>
                        <p id="info-email" class="text-slate-800 font-medium">-</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Alamat:</span>
                        <p id="info-address" class="text-slate-800 font-medium">-</p>
                    </div>
                </div>
            </div>

            {{-- Order History --}}
            <div class="px-5 py-4">
                <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Riwayat Pesanan</h6>
                <div id="order-history" class="space-y-3">
                    <p class="text-sm text-slate-400 text-center py-4">Belum ada pesanan</p>
                </div>
            </div>
        </div>
    </div>

</div>

</div>

{{-- MODAL REASSIGN CS (Admin Only) --}}
@if(auth()->user()->role === 'admin')
<div id="modal-reassign-cs" class="hidden fixed inset-0 z-[60] bg-black/50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden animate-fade-in-up">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800">Reassign CS</h3>
            <button onclick="closeReassignModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <form id="form-reassign-cs" onsubmit="saveReassignment(event)" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih CS</label>
                <select id="select-cs-user" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-[#84994F]/20 focus:border-[#84994F] outline-none">
                    <option value="">-- Pilih CS --</option>
                    @foreach($allCsUsers ?? [] as $csUser)
                    <option value="{{ $csUser->id }}">{{ $csUser->name }} ({{ $csUser->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-3 flex justify-end gap-3">
                <button type="button" onclick="closeReassignModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#84994F] hover:bg-[#6b7d3f] text-white rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                    <i data-lucide="save" class="h-4 w-4"></i> Assign
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- MODAL EDIT CUSTOMER --}}
<div id="modal-edit-customer" class="hidden fixed inset-0 z-[60] bg-black/50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden animate-fade-in-up">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800">Data Pelanggan</h3>
            <button onclick="closeEditCustomerModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <form id="form-edit-customer" onsubmit="saveCustomer(event)" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="input-cust-name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-[#84994F]/20 focus:border-[#84994F] outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor WhatsApp</label>
                <input type="text" id="input-cust-phone" readonly class="w-full px-3 py-2 border border-slate-200 bg-slate-50 rounded-lg text-sm text-slate-500 outline-none cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label>
                <input type="email" id="input-cust-email" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-[#84994F]/20 focus:border-[#84994F] outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kota</label>
                    <input type="text" id="input-cust-city" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-[#84994F]/20 focus:border-[#84994F] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alamat</label>
                    <input type="text" id="input-cust-address" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-[#84994F]/20 focus:border-[#84994F] outline-none">
                </div>
            </div>
            <div class="pt-3 flex justify-end gap-3">
                <button type="button" onclick="closeEditCustomerModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#84994F] hover:bg-[#6b7d3f] text-white rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                    <i data-lucide="save" class="h-4 w-4"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
{{-- Emoji Picker --}}
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>


<script>
    // --- GLOBAL STATE ---
    let currentRoomId = null;
    let currentCustomer = null;
    let allChatRooms = []; // Store all rooms for searching

    // --- HELPER: Init Lucide Icons Safely ---
    function initLucide() {
    if (typeof window.lucide !== 'undefined' && 
        window.lucide.createIcons && 
        window.lucide.icons) {
        try {
            window.lucide.createIcons({ icons: window.lucide.icons });
        } catch (e) {
            console.error('Lucide init error:', e);
        }
    } else {
        console.warn('Lucide not fully loaded. Available:', window.lucide);
    }
}

    // --- SEARCH FUNCTIONALITY ---
    document.getElementById('search-chat').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        filterChatList(query);
    });

    function filterChatList(query) {
        if (!query) {
            renderChatList(allChatRooms);
            return;
        }

        const filtered = allChatRooms.filter(room => {
            const customer = room.customer || {};
            const name = (customer.name || '').toLowerCase();
            const phone = (customer.phone || '').toLowerCase();
            return name.includes(query) || phone.includes(query);
        });

        renderChatList(filtered);
    }

    // --- 1. LOAD CHAT DATA (Initially + on Click) ---
    function loadChat(roomId, element) {
        currentRoomId = roomId;
        
        // RESET lastMessageId when switching rooms
        lastMessageId = 0;
        
        // Optimistic UI Update: Mark as read locally
        const roomIndex = allChatRooms.findIndex(r => r.id === roomId);
        if (roomIndex !== -1) {
            allChatRooms[roomIndex].unread_count = 0;
            allChatRooms[roomIndex].status = 'read'; // Optional: update status too if needed
            
            // Re-render list to remove badge immediately
            const searchInput = document.getElementById('search-chat');
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            if (query) {
                filterChatList(query);
            } else {
                renderChatList(allChatRooms);
            }
        }

        // Trigger notification update in header (if function exists)
        if (typeof pollNotifications === 'function') {
            setTimeout(pollNotifications, 1000); // Delay slightly to allow backend to process
        }
        
        // Mark active in list
        document.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active', 'bg-slate-100'));
        if (element) {
            element.classList.add('active', 'bg-slate-100');
        }

        // Show active chat, hide empty state
        const emptyState = document.getElementById('empty-state');
        const activeChat = document.getElementById('active-chat');
        const chatLoading = document.getElementById('chat-loading');
        
        emptyState.style.display = 'none';
        activeChat.classList.add('show');
        
        // Show loading
        chatLoading.classList.remove('hidden');
        chatLoading.style.display = 'flex'; 
        
        // Clear previous messages
        document.getElementById('messages-container').innerHTML = '';
        
        // Fetch chat data - IMPORTANT: WITHOUT polling parameter to get ALL messages
        fetch(`/app/chat/room/${roomId}/data`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            })
            .then(data => {
                
                if (!data.room) {
                    console.error('Room data not found');
                    chatLoading.classList.add('hidden');
                    alert('Chat room not found');
                    return;
                }
                
                // Customer is in room.customer, not data.customer
                const customer = data.room.customer || {
                    name: 'Unknown',
                    phone: 'Unknown',
                    email: '-',
                    address: '-',
                    city: '-',
                    is_contact: true // Assume contact if unknown
                };
                
                currentCustomer = customer;
                
                // Update header with proper avatar
                const initial = customer.name ? customer.name.charAt(0).toUpperCase() : '?';
                const avatarEl = document.getElementById('chat-avatar');
                avatarEl.innerText = initial;
                avatarEl.className = 'w-10 h-10 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold text-lg';
                
                document.getElementById('chat-name').innerText = customer.name || 'Unknown';
                document.getElementById('chat-phone').innerText = customer.phone || '-';
                
                // Display CS Assignment Badge
                const csBadge = document.getElementById('cs-badge');
                const csNameBadge = document.getElementById('cs-name-badge');
                const btnReassign = document.getElementById('btn-reassign-cs');
                
                if (data.room.cs_user) {
                    csNameBadge.innerText = data.room.cs_user.name.split(' ')[0];
                    csBadge.classList.remove('hidden');
                } else {
                    csBadge.classList.add('hidden');
                }
                
                // Show reassign button for admin
                if (btnReassign) {
                    btnReassign.classList.remove('hidden');
                }

                // Update info panel with proper avatar
                const infoAvatarEl = document.getElementById('info-avatar');
                infoAvatarEl.innerText = initial;
                infoAvatarEl.className = 'w-20 h-20 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold text-2xl mx-auto mb-3';
                
                document.getElementById('info-name').innerText = customer.name || 'Unknown';
                document.getElementById('info-phone').innerText = customer.phone || '-';
                document.getElementById('info-email').innerText = customer.email || '-';
                document.getElementById('info-address').innerText = customer.address || '-';

                // [UPDATE] Button Visibility Logic
                const btnEdit = document.getElementById('btn-edit-customer');
                if (btnEdit) {
                    const btnText = btnEdit.querySelector('span');
                    
                    // Always show button
                    btnEdit.classList.remove('hidden');
                    
                    if (customer.id) {
                        if (customer.is_contact) {
                            // CASE: ChatContact (Unsaved) -> "Tambahkan sebagai Pelanggan" -> Redirect to Create
                            if (btnText) btnText.innerText = 'Tambahkan sebagai Pelanggan';
                            btnEdit.onclick = function() {
                                const safePhone = (customer.phone && customer.phone !== 'Unknown') ? customer.phone : '';
                                const safeName = (customer.name && customer.name !== 'Unknown') ? customer.name : '';
                                window.location.href = `{{ route('customers.create') }}?phone=${encodeURIComponent(safePhone)}&name=${encodeURIComponent(safeName)}`;
                            };
                        } else {
                            // CASE: Existing Customer -> "Edit Pelanggan" -> Open Modal
                            if (btnText) btnText.innerText = 'Edit Pelanggan';
                            btnEdit.onclick = function() { openEditCustomerModal(); };
                        }
                    } else {
                        // CASE: Ghost room / Unknown -> "Tambahkan sebagai Pelanggan" -> Redirect to Create
                        if (btnText) btnText.innerText = 'Tambahkan sebagai Pelanggan';
                        btnEdit.onclick = function() {
                            // Fallback: Try to get from DOM but sanitise "Unknown"
                            let phone = document.getElementById('chat-phone').innerText;
                            let name = document.getElementById('chat-name').innerText;
                            
                            if (phone === 'Unknown' || phone === '-') phone = '';
                            if (name === 'Unknown') name = '';

                            window.location.href = `{{ route('customers.create') }}?phone=${encodeURIComponent(phone)}&name=${encodeURIComponent(name)}`;
                        };
                    }
                }

                // Render messages - IMPORTANT: Load all messages initially
                const allMessages = data.messages || [];
                
                renderChatUI(allMessages);
                
                // Update lastMessageId to the latest message ID
                if (allMessages.length > 0) {
                    lastMessageId = Math.max(...allMessages.map(m => m.id || 0));
                }

                // Render orders
                try {
                    renderOrderHistory(data.orders || []);
                } catch (e) {
                    console.error('Error rendering orders:', e);
                }

                // Re-initialize icons
                initLucide();

                // Start polling for new messages
                pollActiveChat();
            })
            .catch(err => {
                console.error('Error loading chat:', err);
                alert('Gagal memuat chat: ' + err.message);
            })
            .finally(() => {
                // FORCE HIDE LOADING using inline style to override CSS classes
                const loader = document.getElementById('chat-loading');
                if (loader) {
                    loader.classList.add('hidden');
                    loader.style.display = 'none'; // Force override
                }
            });
    }

    function renderChatUI(messages) {
        const container = document.getElementById('messages-container');
        container.innerHTML = '';
        
        if (!messages || messages.length === 0) {
            container.innerHTML = '<div class="text-center text-slate-400 py-8"><p class="text-sm">Belum ada pesan</p></div>';
            return;
        }

        let lastDate = null;
        window.lastRenderedDate = null; // For polling

        messages.forEach((msg, index) => {
            // Date separator
            const msgDate = new Date(msg.created_at).toDateString();
            if (msgDate !== lastDate) {
                const separator = formatDateSeparator(msg.created_at);
                container.insertAdjacentHTML('beforeend', `
                    <div class="text-center text-slate-500 my-3">
                        <span class="inline-block bg-white px-3 py-1 rounded-lg text-xs shadow-sm">${separator}</span>
                    </div>
                `);
                lastDate = msgDate;
                window.lastRenderedDate = msgDate;
            }

            const isMe = msg.sender_type === 'user';
            const bubbleClass = isMe ? 'justify-end' : 'justify-start';
            const bgClass = isMe ? 'bg-[#d9fdd3] rounded-tr-none' : 'bg-white rounded-tl-none';
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false});
            
            let statusIcon = '';
            if (isMe) {
                statusIcon = '<i data-lucide="check" class="h-3 w-3"></i>';
                if (msg.status === 'sent') statusIcon = '<i data-lucide="check-check" class="h-3 w-3 text-slate-600"></i>';
                if (msg.status === 'read') statusIcon = '<i data-lucide="check-check" class="h-3 w-3 text-blue-500"></i>';
            }

            let attachmentHtml = '';
            if (msg.attachment_url) {
                if (msg.attachment_type === 'image') {
                    attachmentHtml = `<div class="mb-2"><img src="${msg.attachment_url}" class="rounded-lg max-w-[200px] max-h-[200px] object-cover"></div>`;
                } else {
                    attachmentHtml = `<div class="mb-2"><a href="${msg.attachment_url}" target="_blank" class="flex items-center gap-2 p-2 bg-slate-100 rounded-lg hover:bg-slate-200 transition text-sm"><i data-lucide="file" class="h-4 w-4"></i><span>Attachment</span></a></div>`;
                }
            }

            const html = `
                <div class="flex ${bubbleClass}">
                    <div class="max-w-[70%] px-3 py-2 rounded-lg shadow-sm ${bgClass}">
                        ${attachmentHtml}
                        <div class="text-sm leading-5 text-slate-800" style="word-break: break-word; overflow-wrap: anywhere;">${escapeHtml(msg.message_content || '')}</div>
                        <div class="flex items-center justify-end gap-1 mt-1" style="font-size: 11px; color: #667781;">
                            ${time} ${statusIcon}
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });

        // Scroll to bottom
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    }

    function renderOrderHistory(orders) {
        const orderList = document.getElementById('order-history');
        orderList.innerHTML = '';
        
        if (!orders || orders.length === 0) {
            orderList.innerHTML = '<p class="text-sm text-slate-400 text-center py-4">Belum ada pesanan</p>';
            return;
        }

        orders.forEach(order => {
            const statusColors = {
                'pending': 'bg-yellow-100 text-yellow-700 border-yellow-200',
                'shipped': 'bg-blue-100 text-blue-700 border-blue-200',
                'completed': 'bg-green-100 text-green-700 border-green-200',
                'cancelled': 'bg-red-100 text-red-700 border-red-200'
            };
            const statusColor = statusColors[order.status] || 'bg-slate-100 text-slate-600 border-slate-200';

            const html = `
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 hover:border-slate-200 transition">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold text-slate-600">#${order.order_number}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full ${statusColor} border font-bold">${order.status.toUpperCase()}</span>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Rp ${order.total_amount}</p>
                    <p class="text-xs text-slate-500 mt-1">${order.date}</p>
                </div>
            `;
            orderList.insertAdjacentHTML('beforeend', html);
        });
    }

    // --- 2. SEND MESSAGE & EMOJI & FILE ---
    function toggleEmojiPicker() {
        const picker = document.getElementById('emoji-picker-container');
        if (picker) picker.classList.toggle('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
    // Tunggu sebentar untuk memastikan Vite sudah load
    setTimeout(() => {
        initLucide();
    }, 100);

    // Emoji picker listener
    const picker = document.querySelector('emoji-picker');
    if (picker) {
        picker.addEventListener('emoji-click', event => {
            const input = document.getElementById('message-input');
            if (input) {
                input.value += event.detail.unicode;
                input.focus();
            }
        });
    }
});

    let selectedFile = null;

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            selectedFile = input.files[0];
            const previewContainer = document.getElementById('file-preview-container');
            const previewName = document.getElementById('file-preview-name');
            if (previewContainer && previewName) {
                previewContainer.classList.remove('hidden');
                previewName.innerText = selectedFile.name;
                initLucide();
            }
        }
    }

    function clearFile() {
        selectedFile = null;
        const fileInput = document.getElementById('file-input');
        if (fileInput) fileInput.value = '';
        const previewContainer = document.getElementById('file-preview-container');
        if (previewContainer) previewContainer.classList.add('hidden');
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
        
        let contentHtml = escapeHtml(message);
        if (selectedFile) {
            contentHtml += `<div class="mt-1 text-xs text-slate-500"><i data-lucide="paperclip" class="h-3 w-3 inline"></i> ${escapeHtml(selectedFile.name)} (Uploading...)</div>`;
        }

        const html = `
            <div class="flex justify-end">
                <div class="max-w-[70%] px-3 py-2 rounded-lg shadow-sm bg-[#d9fdd3] rounded-tr-none opacity-50 sending-message">
                    <div class="text-sm">${contentHtml}</div>
                    <div class="flex items-center justify-end gap-1 mt-1 text-xs text-slate-500">
                        ${time} <i data-lucide="clock" class="h-3 w-3"></i>
                    </div>
                </div>
            </div>
        `;
        msgContainer.insertAdjacentHTML('beforeend', html);
        msgContainer.scrollTop = msgContainer.scrollHeight;
        initLucide();
        
        // Prepare Data
        const formData = new FormData();
        formData.append('message_body', message); 
        if (selectedFile) {
            formData.append('attachment', selectedFile);
        }

        // Reset Input
        input.value = '';
        clearFile();
        const emojiPicker = document.getElementById('emoji-picker-container');
        if (emojiPicker && !emojiPicker.classList.contains('hidden')) toggleEmojiPicker();

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
                // Remove optimistic message and let polling handle it
                const sendingMsg = msgContainer.querySelector('.sending-message');
                if (sendingMsg) {
                    sendingMsg.parentElement.remove();
                }
                // Force immediate poll
                pollActiveChat();
            } else {
                alert('Gagal mengirim pesan: ' + (data.error || 'Unknown error'));
                // Remove failed message
                const sendingMsg = msgContainer.querySelector('.sending-message');
                if (sendingMsg) {
                    sendingMsg.parentElement.remove();
                }
            }
        })
        .catch(err => {
            console.error('Error sending message:', err);
            const sendingMsg = msgContainer.querySelector('.sending-message');
            if (sendingMsg) {
                sendingMsg.parentElement.remove();
            }
        });
    }

    // --- 3. REAL-TIME LONG POLLING ---
    let lastListUpdate = 0;
    let lastGlobalMsgId = 0;
    let lastMessageId = 0;
    let isPollingList = false;
    let isPollingChat = false;

    // Start Polling - CHANGED: Call initial load first, then start polling
    loadChatListInitial();

    // A. Initial Load (without polling parameter)
    function loadChatListInitial() {
        
        fetch(`/app/chat/rooms`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                
                if (data.rooms) {
                    lastListUpdate = data.last_updated_at || Date.now();
                    lastGlobalMsgId = data.last_global_msg_id || 0;
                    
                    // Store globally
                    allChatRooms = data.rooms;

                    // [FIX] Frontend Sorting for Initial Load: Ensure correct order on refresh
                    allChatRooms.sort((a, b) => {
                        const timeA = a.latest_message ? new Date(a.latest_message.created_at).getTime() : new Date(a.updated_at).getTime();
                        const timeB = b.latest_message ? new Date(b.latest_message.created_at).getTime() : new Date(b.updated_at).getTime();
                        return timeB - timeA; // Descending
                    });

                    renderChatList(allChatRooms);
                    
                    // Now start polling for updates
                    setTimeout(pollChatList, 2000);
                } else {
                    console.error('No rooms data in response');
                    showChatListError('Gagal memuat daftar chat');
                }
            })
            .catch(err => {
                console.error('Error loading initial chat list:', err);
                showChatListError('Gagal memuat chat: ' + err.message);
            });
    }

    // Helper: Render Chat List
    function renderChatList(rooms) {
        const listBody = document.getElementById('chat-list');
        
        if (!rooms || rooms.length === 0) {
            listBody.innerHTML = `
                <div class="flex items-center justify-center h-full text-slate-400">
                    <div class="text-center">
                        <i data-lucide="inbox" class="h-12 w-12 mx-auto mb-3 opacity-50"></i>
                        <p class="text-sm">Belum ada percakapan</p>
                    </div>
                </div>
            `;
            initLucide();
            return;
        }
        
        let html = '';
        rooms.forEach(room => {
            const isActive = (currentRoomId == room.id);
            // Use unread_count from backend, BUT ignore if active (we are reading it)
            const hasUnread = (room.unread_count > 0) && !isActive;
            const unreadClass = hasUnread ? 'unread' : '';
            
            const latestMsgObj = room.latest_message;
            const displayTime = latestMsgObj ? latestMsgObj.created_at : room.updated_at;
            const time = formatChatListTime(displayTime);
            const customerName = room.customer.name || room.customer.phone || 'Unknown';
            const latestMsg = latestMsgObj ? (latestMsgObj.message_content || 'File') : room.customer.phone;
            const initial = customerName.charAt(0).toUpperCase();
            
            // CS Assignment Badge
            const csUser = room.cs_user;
            const csBadge = csUser ? `
                <div class="flex items-center gap-1 mt-1">
                    <span class="text-[9px] px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded-md font-bold border border-blue-100">
                        <i data-lucide="user-check" class="h-2.5 w-2.5 inline"></i> ${escapeHtml(csUser.name.split(' ')[0])}
                    </span>
                </div>
            ` : '';

            html += `
                <div class="chat-item ${unreadClass} px-4 py-3 border-b border-slate-50 cursor-pointer hover:bg-slate-50 transition ${isActive ? 'bg-slate-100 active' : ''}" onclick="loadChat(${room.id}, this)">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold flex-shrink-0">
                            ${initial}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-1">
                                <h6 class="font-bold text-slate-800 text-sm truncate ${hasUnread ? 'font-extrabold' : ''}">${escapeHtml(customerName)}</h6>
                                <small class="text-slate-400 text-xs flex-shrink-0 ml-2">${time}</small>
                            </div>
                            <small class="text-slate-500 text-xs truncate block ${hasUnread ? 'font-bold text-slate-800' : ''}">${escapeHtml(latestMsg)}</small>
                            ${csBadge}
                        </div>
                        ${hasUnread ? `
                            <div class="flex flex-col items-end gap-1">
                                <div class="w-5 h-5 rounded-full bg-[#84994F] text-white text-[10px] font-bold flex items-center justify-center">
                                    ${room.unread_count > 99 ? '99+' : room.unread_count}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        
        listBody.innerHTML = html;
        initLucide(); // Re-init icons for badges
    }

    // Helper: Show Chat List Error
    function showChatListError(message) {
        const listBody = document.getElementById('chat-list');
        listBody.innerHTML = `
            <div class="flex items-center justify-center h-full text-red-500">
                <div class="text-center">
                    <i data-lucide="alert-circle" class="h-12 w-12 mx-auto mb-3"></i>
                    <p class="text-sm font-medium">${message}</p>
                    <button onclick="location.reload()" class="mt-3 px-4 py-2 bg-red-500 text-white rounded-lg text-xs hover:bg-red-600">
                        Refresh
                    </button>
                </div>
            </div>
        `;
        initLucide();
    }

    // A. Polling List Chat
    function pollChatList() {
        if (isPollingList) return;
        isPollingList = true;

        fetch(`/app/chat/rooms?last_updated_at=${lastListUpdate}&last_global_msg_id=${lastGlobalMsgId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'no_update') {
                    isPollingList = false;
                    setTimeout(pollChatList, 2000);
                    return;
                }

                if (data.rooms) {
                    lastListUpdate = data.last_updated_at;
                    lastGlobalMsgId = data.last_global_msg_id || lastGlobalMsgId;
                    
                    // Update global data
                    allChatRooms = data.rooms;

                    // [BARU] Frontend Sorting: Pastikan yang terbaru selalu di atas
                    // Sort berdasarkan latest_message.created_at atau updated_at
                    allChatRooms.sort((a, b) => {
                        const timeA = a.latest_message ? new Date(a.latest_message.created_at).getTime() : new Date(a.updated_at).getTime();
                        const timeB = b.latest_message ? new Date(b.latest_message.created_at).getTime() : new Date(b.updated_at).getTime();
                        return timeB - timeA; // Descending (Paling baru di atas)
                    });
                    
                    // Check if search is active
                    const searchInput = document.getElementById('search-chat');
                    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
                    
                    if (query) {
                        filterChatList(query);
                    } else {
                        renderChatList(allChatRooms);
                    }

                    // [BARU] Trigger update notifikasi di header agar sinkron
                    if (typeof pollNotifications === 'function') {
                        pollNotifications();
                    }
                }

                isPollingList = false;
                setTimeout(pollChatList, 2000);
            })
            .catch(err => {
                console.error('Polling List Error:', err);
                isPollingList = false;
                setTimeout(pollChatList, 5000);
            });
    }

    // B. Polling Active Chat
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

                if (data.messages && data.messages.length > 0) {
                    lastMessageId = data.last_message_id;
                    const messages = data.messages;
                    const msgContainer = document.getElementById('messages-container');
                    
                    const isAtBottom = (msgContainer.scrollHeight - msgContainer.scrollTop) <= (msgContainer.clientHeight + 50);

                    // Render new messages
                    messages.forEach(msg => {
                        const msgDate = new Date(msg.created_at).toDateString();
                        
                        if (typeof window.lastRenderedDate === 'undefined') window.lastRenderedDate = null;
                        
                        if (msgDate !== window.lastRenderedDate) {
                            msgContainer.insertAdjacentHTML('beforeend', `
                                <div class="text-center text-slate-500 my-3">
                                    <span class="inline-block bg-white px-3 py-1 rounded-lg text-xs shadow-sm">${formatDateSeparator(msg.created_at)}</span>
                                </div>
                            `);
                            window.lastRenderedDate = msgDate;
                        }

                        const isMe = msg.sender_type != 'customer';
                        const bubbleClass = isMe ? 'justify-end' : 'justify-start';
                        const bgClass = isMe ? 'bg-[#d9fdd3] rounded-tr-none' : 'bg-white rounded-tl-none';
                        const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false});
                        
                        let statusIcon = '';
                        if (isMe) {
                            statusIcon = '<i data-lucide="check" class="h-3 w-3"></i>';
                            if (msg.status === 'sent') statusIcon = '<i data-lucide="check-check" class="h-3 w-3 text-slate-600"></i>';
                            if (msg.status === 'read') statusIcon = '<i data-lucide="check-check" class="h-3 w-3 text-blue-500"></i>';
                        }

                        let attachmentHtml = '';
                        if (msg.attachment_url) {
                            if (msg.attachment_type === 'image') {
                                attachmentHtml = `<div class="mb-2"><img src="${msg.attachment_url}" class="rounded-lg max-w-[200px] max-h-[200px] object-cover"></div>`;
                            } else {
                                attachmentHtml = `<div class="mb-2"><a href="${msg.attachment_url}" target="_blank" class="flex items-center gap-2 p-2 bg-slate-100 rounded-lg hover:bg-slate-200 transition text-sm"><i data-lucide="file" class="h-4 w-4"></i><span>Attachment</span></a></div>`;
                            }
                        }

                        const html = `
                            <div class="flex ${bubbleClass}">
                                <div class="max-w-[70%] px-3 py-2 rounded-lg shadow-sm ${bgClass}">
                                    ${attachmentHtml}
                                    <div class="text-sm leading-5 text-slate-800 break-words">${escapeHtml(msg.message_content || '')}</div>
                                    <div class="flex items-center justify-end gap-1 mt-1" style="font-size: 11px; color: #667781;">
                                        ${time} ${statusIcon}
                                    </div>
                                </div>
                            </div>
                        `;
                        msgContainer.insertAdjacentHTML('beforeend', html);
                    });

                    initLucide();

                    if (isAtBottom) {
                        msgContainer.scrollTop = msgContainer.scrollHeight;
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

    // --- HELPER FUNCTIONS ---
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
            return date.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: '2-digit'});
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
            return date.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Toggle Info Panel
    function toggleInfoPanel() {
        const panel = document.getElementById('info-panel');
        panel.classList.toggle('hidden');
        panel.classList.toggle('flex');
        initLucide();
    }


    // --- CUSTOMER MODAL FUNCTIONS ---
    function openEditCustomerModal() {
        if (!currentCustomer) return;
        
        const modal = document.getElementById('modal-edit-customer');
        const form = document.getElementById('form-edit-customer');
        
        // Fill form
        document.getElementById('input-cust-name').value = (currentCustomer.name === currentCustomer.phone) ? '' : currentCustomer.name;
        
        const phoneInput = document.getElementById('input-cust-phone');
        phoneInput.value = currentCustomer.phone;
        
        // Allow editing phone if unknown
        if (currentCustomer.phone === 'Unknown' || !currentCustomer.phone) {
            phoneInput.readOnly = false;
            phoneInput.classList.remove('bg-slate-50', 'cursor-not-allowed');
            phoneInput.value = ''; // Clear it
        } else {
            phoneInput.readOnly = true;
            phoneInput.classList.add('bg-slate-50', 'cursor-not-allowed');
        }

        document.getElementById('input-cust-email').value = currentCustomer.email || '';
        document.getElementById('input-cust-city').value = currentCustomer.city || '';
        document.getElementById('input-cust-address').value = currentCustomer.address || '';
        
        modal.classList.remove('hidden');
    }

    function closeEditCustomerModal() {
        document.getElementById('modal-edit-customer').classList.add('hidden');
    }

    function saveCustomer(e) {
        e.preventDefault();
        if (!currentCustomer) return;

        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Menyimpan...';
        initLucide();

        const payload = {
            name: document.getElementById('input-cust-name').value,
            phone: document.getElementById('input-cust-phone').value, // Include phone
            email: document.getElementById('input-cust-email').value,
            city: document.getElementById('input-cust-city').value,
            address: document.getElementById('input-cust-address').value,
            is_contact: currentCustomer.is_contact,
            _token: '{{ csrf_token() }}'
        };

        let url;
        if (currentCustomer.id) {
             url = `/app/chat/customer/${currentCustomer.id}/update`;
        } else {
             // New endpoint for ghost rooms
             url = `/app/chat/room/${currentRoomId}/create-customer`;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update local data
                currentCustomer = data.customer;
                
                // Update UI Info Panel
                const customerName = currentCustomer.name || currentCustomer.phone;
                document.getElementById('info-name').innerText = customerName;
                document.getElementById('info-avatar').innerText = customerName.charAt(0).toUpperCase();
                document.getElementById('info-email').innerText = currentCustomer.email || '-';
                document.getElementById('info-address').innerText = currentCustomer.address || '-';
                
                // Update Button Text
                const btnEdit = document.getElementById('btn-edit-customer');
                const btnText = btnEdit.querySelector('span');
                btnText.innerText = 'Edit Pelanggan';

                // Update List Chat (Name might change)
                const roomIndex = allChatRooms.findIndex(r => r.id == currentRoomId);
                if (roomIndex !== -1) {
                    allChatRooms[roomIndex].customer.name = currentCustomer.name;
                    renderChatList(allChatRooms);
                }

                closeEditCustomerModal();
                // Show success toast (optional)
                alert('Data pelanggan berhasil disimpan!');
            } else {
                alert('Gagal menyimpan: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat menyimpan data.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        initLucide();
        });
    }

    // --- REASSIGN CS MODAL (Admin Only) ---
function openReassignModal() {
    const modal = document.getElementById('modal-reassign-cs');
    if (modal) {
        modal.classList.remove('hidden');
        initLucide();
    }
}
function closeReassignModal() {
    const modal = document.getElementById('modal-reassign-cs');
    if (modal) {
        modal.classList.add('hidden');
        const form = document.getElementById('form-reassign-cs');
        if (form) form.reset();
    }
}
function saveReassignment(event) {
    event.preventDefault();
    
    const csUserId = document.getElementById('select-cs-user').value;
    if (!csUserId || !currentRoomId) {
        alert('Pilih CS terlebih dahulu');
        return;
    }
    fetch(`/app/chat/room/${currentRoomId}/reassign`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.getElementById('csrf-token').value
        },
        body: JSON.stringify({ cs_user_id: csUserId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeReassignModal();
            
            // Update CS badge in header
            const csBadge = document.getElementById('cs-badge');
            const csNameBadge = document.getElementById('cs-name-badge');
            if (data.cs_user) {
                csNameBadge.innerText = data.cs_user.name.split(' ')[0];
                csBadge.classList.remove('hidden');
                initLucide();
            }
            
            // Refresh chat list to update sidebar
            pollChatList();
        } else {
            alert('Gagal reassign: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(err => {
        console.error('Error reassigning CS:', err);
        alert('Terjadi kesalahan saat reassign CS');
    });
}
</script>
@endpush

@endsection
