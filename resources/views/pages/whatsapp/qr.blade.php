@extends('layouts.app')

@section('title', 'Koneksi WhatsApp')

@section('content')

    {{-- HEADER PAGE (Tidak berubah) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="qr-code" class="h-6 w-6 text-[#25D366]"></i> Koneksi WhatsApp
            </h1>
            <p class="text-sm text-slate-500 mt-1">Hubungkan akun WhatsApp Anda untuk mengelola chat pelanggan.</p>
        </div>
    </div>

    {{-- KONTEN UTAMA (DIREVISI) --}}
    <div class="flex justify-center pb-10">
        {{-- GANTI DARI max-w-3xl menjadi w-full --}}
        <div class="w-full"> 

            {{-- MAIN CARD (Dibiarkan lebar penuh) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden min-h-[400px]">
                
                {{-- Card Header --}}
                <div class="text-center p-8 border-b border-slate-50 bg-slate-50/50">
                    <i data-lucide="smartphone" class="h-12 w-12 text-[#25D366] mx-auto mb-3"></i>
                    <h3 class="text-xl font-bold text-slate-800">WhatsApp Web</h3>
                    <p class="text-sm text-slate-500">Status koneksi perangkat server</p>
                </div>

                {{-- Alert Error (Jika ada) --}}
                @if(isset($error))
                    <div class="mx-8 mt-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="h-5 w-5 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <p class="font-bold text-sm">{{ $error }}</p>
                            <p class="text-xs mt-1 opacity-80">Pastikan WhatsApp Service berjalan di: {{ $waServiceUrl }}</p>
                        </div>
                    </div>
                @endif

                {{-- STATUS CONTAINER (Konten di dalamnya menyesuaikan) --}}
                <div class="p-8" id="connectionStatusContainer">
                    
                    {{-- 1. STATE: LOADING --}}
                    <div id="state-loading" class="text-center py-8 hidden">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-[#84994F] mb-4"></div>
                        <h5 class="font-bold text-slate-700" id="loading-text">Menginisialisasi...</h5>
                        <p class="text-sm text-slate-400">Mohon tunggu sebentar...</p>
                    </div>

                    {{-- 2. STATE: CONNECTED --}}
                    <div id="state-connected" class="text-center hidden">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-100 mb-6">
                            <i data-lucide="check-circle-2" class="h-16 w-16 text-green-600 mx-auto mb-4"></i>
                            <h4 class="text-2xl font-bold text-green-800">WhatsApp Terhubung!</h4>
                            <p class="text-green-600 text-sm mb-6">Akun WhatsApp Anda sudah terhubung dan siap digunakan.</p>
                            
                            <div class="bg-white rounded-xl border border-green-100 p-4 text-left shadow-sm max-w-sm mx-auto space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-slate-400 uppercase">Nomor</span>
                                    <span class="font-mono font-bold text-slate-700" id="connected-phone">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-slate-400 uppercase">Sejak</span>
                                    <span class="text-xs text-slate-600 font-medium" id="connected-time">-</span>
                                </div>
                            </div>
                        </div>

                        <button onclick="disconnectWhatsApp()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-bold hover:bg-red-50 transition shadow-sm">
                            <i data-lucide="x-circle" class="h-4 w-4"></i> Putuskan Koneksi
                        </button>
                    </div>
                    
                    {{-- 3. STATE: QR CODE --}}
                    <div id="state-qr" class="text-center hidden">
                        <div class="bg-slate-900 rounded-2xl p-6 text-white mb-6 max-w-md mx-auto">
                            <h4 class="text-lg font-bold mb-2">Scan QR Code</h4>
                            <p class="text-sm text-slate-300 mb-4">Buka WhatsApp di HP > Menu > Perangkat Tertaut > Tautkan Perangkat</p>
                            
                            <div class="bg-white p-3 rounded-xl inline-block">
                                <div id="qrcode" class="w-[256px] h-[256px] flex items-center justify-center text-slate-300 bg-slate-100"></div>
                            </div>
                            
                            <p class="text-xs text-slate-400 mt-4 animate-pulse">QR Code diperbarui otomatis</p>
                        </div>

                        <button onclick="reconnectWhatsApp()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-[#84994F] text-[#84994F] rounded-lg text-sm font-bold hover:bg-green-50 transition">
                            <i data-lucide="refresh-cw" class="h-4 w-4"></i> Generate QR Baru
                        </button>
                    </div>

                    {{-- 4. STATE: DISCONNECTED --}}
                    <div id="state-disconnected" class="text-center py-8 hidden">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 text-red-500 mb-4">
                            <i data-lucide="wifi-off" class="h-8 w-8"></i>
                        </div>
                        <h5 class="text-lg font-bold text-slate-800">WhatsApp Terputus</h5>
                        <p class="text-sm text-slate-500 mb-6">Koneksi WhatsApp telah terputus. Klik tombol di bawah untuk menyambungkan kembali.</p>
                        
                        <button onclick="reconnectWhatsApp()" class="inline-flex items-center gap-2 px-6 py-3 bg-[#84994F] text-white rounded-xl text-sm font-bold hover:bg-[#6b7d3f] shadow-lg shadow-green-100 transition">
                            <i data-lucide="refresh-cw" class="h-4 w-4"></i> Sambungkan Kembali
                        </button>
                    </div>

                    {{-- 5. STATE: INITIAL --}}
                    <div id="state-initial" class="text-center py-8 hidden">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 text-slate-400 mb-4">
                            <i data-lucide="smartphone-nfc" class="h-10 w-10"></i>
                        </div>
                        <h5 class="text-lg font-bold text-slate-800">Belum Terhubung</h5>
                        <p class="text-sm text-slate-500 mb-6">Mulai koneksi WhatsApp untuk melanjutkan.</p>
                        
                        <button onclick="startWhatsApp()" class="inline-flex items-center gap-2 px-8 py-3 bg-[#84994F] text-white rounded-xl text-sm font-bold hover:bg-[#6b7d3f] shadow-lg shadow-green-100 transition hover:scale-105 transform duration-200">
                            <i data-lucide="play" class="h-4 w-4"></i> Mulai Koneksi
                        </button>
                    </div>

                </div>
            </div>

            {{-- INSTRUCTIONS --}}
            <div class="mt-6 bg-white rounded-xl border border-slate-100 p-6">
                <h6 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="help-circle" class="h-5 w-5 text-[#FCB53B]"></i> Cara Menghubungkan
                </h6>
                <ol class="list-decimal list-inside space-y-2 text-sm text-slate-600 marker:text-[#84994F] marker:font-bold pl-2">
                    <li>Klik tombol <strong>"Mulai Koneksi"</strong>.</li>
                    <li>Tunggu hingga <strong>QR Code</strong> muncul.</li>
                    <li>Buka aplikasi WhatsApp di ponsel Anda.</li>
                    <li>Buka menu <strong>Settings</strong> > <strong>Perangkat Tertaut</strong>.</li>
                    <li>Scan QR Code yang tampil di layar ini.</li>
                </ol>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // --- VARIABLES ---
    let statusCheckInterval = null;
    let currentQrData = null;
    let currentStatus = null;
    let isActionProcessing = false;
    let failureCount = 0;
    const MAX_FAILURES = 3;
    let autoReconnectCount = 0;
    const MAX_AUTO_RECONNECT = 5;

    const initialAccount = @json($account ?? null);

    // --- HELPER FUNCTIONS ---
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function showElement(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }

    function hideAllStates() {
        ['state-loading', 'state-connected', 'state-qr', 'state-disconnected', 'state-initial'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        });
    }

    // --- UI UPDATE LOGIC ---
    function updateUI(status, data = {}) {
        // Debounce & Auto Reconnect Logic
        if (currentStatus === 'QR' && (status === 'DISCONNECTED' || status === 'INITIAL')) {
            failureCount++;
            if (failureCount < MAX_FAILURES) return;
            if (autoReconnectCount < MAX_AUTO_RECONNECT) {
                autoReconnectCount++;
                failureCount = 0;
                startWhatsApp(true);
                return;
            }
        } else {
            if (status === 'READY' || status === 'QR') {
                failureCount = 0;
                if (status === 'READY') autoReconnectCount = 0;
            }
        }

        currentStatus = status;
        hideAllStates();
        console.log('Updating UI:', status);

        if (status === 'READY' || status === 'CONNECTED') {
            showElement('state-connected');
            if (data.phoneNumber) {
                let phone = data.phoneNumber.replace(/\D/g, '');
                document.getElementById('connected-phone').innerText = '+' + phone;
            }
            if (data.connectedAt) {
                const date = new Date(data.connectedAt);
                document.getElementById('connected-time').innerText = date.toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' });
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();

        } else if (status === 'QR' || status === 'QR_READY') {
            showElement('state-qr');
            if (data.qr && data.qr !== currentQrData) {
                currentQrData = data.qr;
                showQRCode(currentQrData);
            }
        } else if (status === 'DISCONNECTED') {
            showElement('state-disconnected');
        } else if (status === 'INITIAL' || !status) {
            showElement('state-initial');
        } else {
            showElement('state-loading');
            document.getElementById('loading-text').innerText = 'Status: ' + status;
        }
    }

    function showQRCode(qrString) {
        const qrcodeDiv = document.getElementById('qrcode');
        if (qrcodeDiv) {
            qrcodeDiv.innerHTML = '';
            try {
                new QRCode(qrcodeDiv, {
                    text: qrString,
                    width: 256,
                    height: 256,
                    colorDark : "#1e293b",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.L
                });
            } catch (e) {
                console.error('QR Error:', e);
                qrcodeDiv.innerHTML = '<span class="text-red-500 text-xs">Gagal Generate QR</span>';
            }
        }
    }

    function showLoading(message) {
        hideAllStates();
        showElement('state-loading');
        document.getElementById('loading-text').innerText = message;
    }

    // --- API CALLS ---
    function apiRequest(url) {
        isActionProcessing = true;
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        }).then(res => res.json());
    }

    function startWhatsApp(silent = false) {
        if (!silent) showLoading('Menghubungkan...');
        apiRequest('/admin/whatsapp/api/start')
            .then(data => {
                if (data.success) checkStatus();
                else { if(!silent) alert('Gagal: ' + data.message); isActionProcessing = false; checkStatus(); }
            })
            .catch(() => { isActionProcessing = false; checkStatus(); });
    }

    function reconnectWhatsApp() {
        if (!confirm('Reset koneksi dan buat QR baru?')) return;
        showLoading('Membuat QR...');
        apiRequest('/admin/whatsapp/api/reconnect')
            .then(data => {
                if (data.success) setTimeout(() => { isActionProcessing = false; checkStatus(); }, 2000);
                else { alert('Gagal: ' + data.message); isActionProcessing = false; checkStatus(); }
            });
    }

    function disconnectWhatsApp() {
        if (!confirm('Putuskan koneksi?')) return;
        showLoading('Memutuskan...');
        apiRequest('/admin/whatsapp/api/disconnect')
            .then(() => checkStatus());
    }

    function checkStatus() {
        fetch('/admin/whatsapp/api/status?t=' + new Date().getTime())
            .then(res => res.json())
            .then(data => {
                isActionProcessing = false;
                if (data.success && data.account) {
                    updateUI(data.account.status, data.account);
                } else {
                    updateUI('INITIAL');
                }
            })
            .catch(() => {});
    }

    // --- INIT ---
    if (initialAccount) {
        updateUI(initialAccount.status, initialAccount);
    } else {
        updateUI('INITIAL');
    }

    statusCheckInterval = setInterval(() => {
        if (!isActionProcessing) checkStatus();
    }, 3000);

    if (typeof lucide !== 'undefined') lucide.createIcons();
    
    window.addEventListener('beforeunload', () => {
        if (statusCheckInterval) clearInterval(statusCheckInterval);
    });
</script>
@endpush