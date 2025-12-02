@extends('layout.main')

@section('title', 'Koneksi WhatsApp')

@section('search-placeholder', 'Cari kontak atau pesan...')

@section('topbar-actions')
<button class="btn btn-primary" onclick="refreshStatusManual()">
    <i class="bi bi-arrow-clockwise me-1"></i><span class="d-none d-lg-inline">Refresh Status</span>
</button>
@endsection

@push('styles')
<style>
    .connection-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        padding: 2rem;
        min-height: 400px; /* Prevent layout shift */
    }
    
    .qr-container {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        color: white;
    }
    
    .qr-code-box {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        display: inline-block;
        margin: 1rem 0;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .status-ready { background: #d4edda; color: #155724; }
    .status-qr { background: #fff3cd; color: #856404; }
    .status-loading { background: #d1ecf1; color: #0c5460; }
    .status-disconnected { background: #f8d7da; color: #721c24; }
    .status-initializing { background: #e2e3e5; color: #383d41; }
    
    .info-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    
    .spinner-border-custom {
        width: 3rem;
        height: 3rem;
        border-width: 0.3rem;
    }

    /* Transitions */
    .fade-enter {
        opacity: 0;
        transition: opacity 0.3s ease-in;
    }
    .fade-enter-active {
        opacity: 1;
    }
</style>
@endpush

@section('main-content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="connection-card">
            <div class="text-center mb-4">
                <i class="bi bi-whatsapp" style="font-size: 3rem; color: #25D366;"></i>
                <h3 class="mt-3 fw-bold">Koneksi WhatsApp Web</h3>
                <p class="text-muted">Hubungkan akun WhatsApp Anda untuk mengelola chat pelanggan</p>
            </div>

            @if(isset($error))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}
                    <p class="mb-0 mt-2"><small>Pastikan WhatsApp Service sudah berjalan di: {{ $waServiceUrl }}</small></p>
                </div>
            @endif

            <div id="connectionStatusContainer" class="position-relative">
                
                <!-- STATE: LOADING / INITIALIZING -->
                <div id="state-loading" class="text-center d-none">
                    <div class="spinner-border text-primary spinner-border-custom" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mt-3" id="loading-text">Menginisialisasi...</h5>
                    <p class="text-muted">Mohon tunggu sebentar...</p>
                </div>

                <!-- STATE: CONNECTED / READY -->
                <div id="state-connected" class="d-none">
                    <div class="qr-container">
                        <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">WhatsApp Terhubung!</h4>
                        <p class="mb-3">Akun WhatsApp Anda sudah terhubung dan siap digunakan</p>
                        
                        <div class="info-item text-dark mt-3">
                            <strong>Terhubung sejak:</strong> <span id="connected-time">-</span>
                        </div>

                        <button class="btn btn-light mt-3" onclick="disconnectWhatsApp()">
                            <i class="bi bi-x-circle me-2"></i>Putuskan Koneksi
                        </button>
                    </div>
                </div>

                <!-- STATE: QR CODE -->
                <div id="state-qr" class="d-none">
                    <div class="qr-container">
                        <h4>Scan QR Code</h4>
                        <p class="mb-3">Buka WhatsApp di ponsel Anda, masuk ke <strong>Settings → Linked Devices → Link a Device</strong></p>
                        
                        <div class="qr-code-box">
                            <div id="qrcode"></div>
                        </div>

                        <p class="mt-3 mb-0"><small>QR Code akan diperbarui otomatis</small></p>
                    </div>
                </div>

                <!-- STATE: DISCONNECTED -->
                <div id="state-disconnected" class="d-none">
                    <div class="text-center">
                        <div class="status-badge status-disconnected mb-3">
                            <i class="bi bi-x-circle me-2"></i>Terputus
                        </div>
                        <h5>WhatsApp Terputus</h5>
                        <p class="text-muted">Koneksi WhatsApp telah terputus. Klik tombol di bawah untuk menyambungkan kembali.</p>
                        
                        <button class="btn btn-primary mt-3" onclick="reconnectWhatsApp()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Sambungkan Kembali
                        </button>
                    </div>
                </div>

                <!-- STATE: NOT STARTED (Initial) -->
                <div id="state-initial" class="d-none">
                    <div class="text-center">
                        <i class="bi bi-phone" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-3">Belum Terhubung</h5>
                        <p class="text-muted">Mulai koneksi WhatsApp untuk melanjutkan</p>
                        
                        <button class="btn btn-primary btn-lg mt-3" onclick="startWhatsApp()">
                            <i class="bi bi-play-fill me-2"></i>Mulai Koneksi
                        </button>
                    </div>
                </div>

            </div>

            <div class="mt-4 pt-4 border-top">
                <h6 class="fw-bold mb-3">Cara Menghubungkan:</h6>
                <ol class="text-muted">
                    <li>Klik tombol "Mulai Koneksi"</li>
                    <li>Tunggu QR Code muncul</li>
                    <li>Buka WhatsApp di ponsel Anda</li>
                    <li>Ketuk Menu (⋮) atau Settings</li>
                    <li>Pilih "Linked Devices" / "Perangkat Tertaut"</li>
                    <li>Ketuk "Link a Device" / "Tautkan Perangkat"</li>
                    <li>Scan QR Code yang muncul di layar ini</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
<script>
let statusCheckInterval = null;
let currentQrData = null;
let currentStatus = null;
let isActionProcessing = false; // Flag to prevent polling updates during user actions
let failureCount = 0; // Counter for consecutive failures/disconnections
const MAX_FAILURES = 3; // Number of failures before showing disconnected UI
let autoReconnectCount = 0; // Prevent infinite auto-reconnect loops
const MAX_AUTO_RECONNECT = 5; // Max auto-reconnects per session

// Initial Data from Server (Blade)
const initialAccount = @json($account ?? null);

function updateUI(status, data = {}) {
    // 1. DEBOUNCE LOGIC
    // If we are currently in QR mode, and the new status is DISCONNECTED or INITIAL,
    // we should debounce it to prevent flickering.
    if (currentStatus === 'QR' && (status === 'DISCONNECTED' || status === 'INITIAL')) {
        failureCount++;
        console.log(`Potential disconnection detected (${failureCount}/${MAX_FAILURES})...`);
        
        if (failureCount < MAX_FAILURES) {
            return; // Wait for more confirmations
        }
        
        // If we reached max failures, check if we should AUTO-RECONNECT
        if (autoReconnectCount < MAX_AUTO_RECONNECT) {
            console.log('QR Session expired. Auto-reconnecting...');
            autoReconnectCount++;
            failureCount = 0; // Reset failure count
            startWhatsApp(true); // Silent start
            return;
        }
    } else {
        // If status is valid (READY, QR), reset failure counter
        if (status === 'READY' || status === 'QR') {
            failureCount = 0;
            if (status === 'READY') autoReconnectCount = 0; // Reset auto-reconnect on success
        }
    }

    currentStatus = status; // Update current status tracker

    // 2. UI UPDATE LOGIC
    // Hide all states first
    ['state-loading', 'state-connected', 'state-qr', 'state-disconnected', 'state-initial'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });

    console.log('Updating UI to:', status, data);

    if (status === 'READY') {
        document.getElementById('state-connected').classList.remove('d-none');
        if (data.lastConnectedAt) {
            const date = new Date(data.lastConnectedAt);
            document.getElementById('connected-time').innerText = date.toLocaleString('id-ID');
        }
    } else if (status === 'QR') {
        document.getElementById('state-qr').classList.remove('d-none');
        if (data.lastQr && data.lastQr !== currentQrData) {
            currentQrData = data.lastQr;
            showQRCode(currentQrData);
        }
    } else if (status === 'DISCONNECTED') {
        document.getElementById('state-disconnected').classList.remove('d-none');
    } else if (status === 'INITIAL' || !status) {
        document.getElementById('state-initial').classList.remove('d-none');
    } else {
        // Loading or unknown
        document.getElementById('state-loading').classList.remove('d-none');
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
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        } catch (e) {
            console.error('QR Error:', e);
            qrcodeDiv.innerHTML = '<p class="text-danger">Error generating QR</p>';
        }
    }
}

function showLoading(message) {
    // Hide all
    ['state-loading', 'state-connected', 'state-qr', 'state-disconnected', 'state-initial'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
    
    const loadingDiv = document.getElementById('state-loading');
    loadingDiv.classList.remove('d-none');
    document.getElementById('loading-text').innerText = message;
}

// API Actions
function startWhatsApp(silent = false) {
    isActionProcessing = true;
    if (!silent) showLoading('Memulai WhatsApp Service...');
    else showLoading('Memperbarui QR Code...'); // Show friendly message during auto-reconnect
    
    fetch('/api/whatsapp/start', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Start success');
            failureCount = 0; 
            checkStatus();
        } else {
            if (!silent) alert('Gagal memulai WhatsApp: ' + (data.message || 'Unknown error'));
            isActionProcessing = false;
            checkStatus();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (!silent) alert('Terjadi kesalahan saat memulai WhatsApp');
        isActionProcessing = false;
        checkStatus();
    });
}

function reconnectWhatsApp() {
    if (!confirm('Ini akan memutuskan koneksi saat ini dan membuat QR Code baru. Lanjutkan?')) return;
    
    isActionProcessing = true;
    showLoading('Membuat koneksi baru...');
    
    fetch('/api/whatsapp/reconnect', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Reconnect success');
            failureCount = 0;
            checkStatus();
        } else {
            alert('Gagal menyambungkan kembali: ' + (data.message || 'Unknown error'));
            isActionProcessing = false;
            checkStatus();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyambungkan kembali');
        isActionProcessing = false;
        checkStatus();
    });
}

function disconnectWhatsApp() {
    if (!confirm('Anda yakin ingin memutuskan koneksi WhatsApp?')) return;
    
    isActionProcessing = true;
    showLoading('Memutuskan koneksi...');
    
    fetch('/api/whatsapp/disconnect', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Disconnect success');
            checkStatus();
        } else {
            alert('Gagal memutuskan koneksi: ' + (data.message || 'Unknown error'));
            isActionProcessing = false;
            checkStatus();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memutuskan koneksi');
        isActionProcessing = false;
        checkStatus();
    });
}

function checkStatus() {
    fetch('/api/whatsapp/status?t=' + new Date().getTime())
        .then(response => response.json())
        .then(data => {
            isActionProcessing = false; // Reset flag
            if (data.success && data.account) {
                const account = data.account;
                updateUI(account.status, account);
            } else {
                updateUI('INITIAL'); 
            }
        })
        .catch(error => {
            console.error('Status check error:', error);
            updateUI('INITIAL');
        });
}

function refreshStatusManual() {
    showLoading('Memperbarui status...');
    checkStatus();
}

// Initialize
if (initialAccount) {
    updateUI(initialAccount.status, initialAccount);
} else {
    updateUI('INITIAL');
}

// Polling every 2 seconds
statusCheckInterval = setInterval(() => {
    if (!isActionProcessing) {
        checkStatus();
    }
}, 2000);

// Check QRCode lib
if (typeof QRCode === 'undefined') {
    console.error('QRCode library not loaded!');
    alert('Library QR Code gagal dimuat. Silakan refresh halaman.');
}

// Cleanup
window.addEventListener('beforeunload', () => {
    if (statusCheckInterval) {
        clearInterval(statusCheckInterval);
    }
});
</script>
@endpush
