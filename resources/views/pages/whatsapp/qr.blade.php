@extends('layout.main')

@section('title', 'Koneksi WhatsApp')

@section('search-placeholder', 'Cari kontak atau pesan...')

@section('topbar-actions')
<button class="btn btn-primary" onclick="location.reload()">
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

            <div id="connectionStatus">
                @if($account)
                    @if($account['status'] === 'READY')
                        <div class="qr-container">
                            <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">WhatsApp Terhubung!</h4>
                            <p class="mb-3">Akun WhatsApp Anda sudah terhubung dan siap digunakan</p>
                            
                            @if(isset($account['lastConnectedAt']))
                                <div class="info-item text-dark mt-3">
                                    <strong>Terhubung sejak:</strong> {{ \Carbon\Carbon::parse($account['lastConnectedAt'])->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}
                                </div>
                            @endif

                            <button class="btn btn-light mt-3" onclick="disconnectWhatsApp()">
                                <i class="bi bi-x-circle me-2"></i>Putuskan Koneksi
                            </button>
                        </div>

                    @elseif($account['status'] === 'QR' && isset($account['lastQr']))
                        <div class="qr-container">
                            <h4>Scan QR Code</h4>
                            <p class="mb-3">Buka WhatsApp di ponsel Anda, masuk ke <strong>Settings → Linked Devices → Link a Device</strong></p>
                            
                            <div class="qr-code-box">
                                <div id="qrcode"></div>
                            </div>

                            <p class="mt-3 mb-0"><small>QR Code akan diperbarui otomatis setiap 30 detik</small></p>
                        </div>

                    @elseif($account['status'] === 'DISCONNECTED')
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

                    @else
                        <div class="text-center">
                            <div class="spinner-border text-primary spinner-border-custom" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5 class="mt-3">Menginisialisasi...</h5>
                            <p class="text-muted">Status: <span class="status-badge status-loading">{{ $account['status'] }}</span></p>
                        </div>
                    @endif

                @else
                    <div class="text-center">
                        <i class="bi bi-phone" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-3">Belum Terhubung</h5>
                        <p class="text-muted">Mulai koneksi WhatsApp untuk melanjutkan</p>
                        
                        <button class="btn btn-primary btn-lg mt-3" onclick="startWhatsApp()">
                            <i class="bi bi-play-fill me-2"></i>Mulai Koneksi
                        </button>
                    </div>
                @endif
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
let qrData = @json($account['lastQr'] ?? null);

function showQRCode(qrString) {
    const qrcodeDiv = document.getElementById('qrcode');
    if (qrcodeDiv) {
        qrcodeDiv.innerHTML = '';
        QRCode.toCanvas(qrString, { width: 280, margin: 2 }, function (error, canvas) {
            if (error) {
                console.error('QR Error:', error);
                qrcodeDiv.innerHTML = '<p class="text-danger">Error generating QR</p>';
            } else {
                qrcodeDiv.appendChild(canvas);
            }
        });
    }
}

// Show initial QR if available
if (qrData) {
    showQRCode(qrData);
}

function showLoading(message) {
    const container = document.getElementById('connectionStatus');
    if (container) {
        container.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary spinner-border-custom" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="mt-3">${message}</h5>
                <p class="text-muted">Mohon tunggu sebentar...</p>
            </div>
        `;
    }
}

function startWhatsApp() {
    showLoading('Memulai WhatsApp Service...');
    
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
            console.log('Start success, waiting for status update...');
        } else {
            alert('Gagal memulai WhatsApp: ' + (data.message || 'Unknown error'));
            location.reload(); // Reload to reset UI on error
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memulai WhatsApp');
        location.reload();
    });
}

function reconnectWhatsApp() {
    if (!confirm('Ini akan memutuskan koneksi saat ini dan membuat QR Code baru. Lanjutkan?')) return;
    
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
            console.log('Reconnect success, waiting for status update...');
        } else {
            alert('Gagal menyambungkan kembali: ' + (data.message || 'Unknown error'));
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyambungkan kembali');
        location.reload();
    });
}

function disconnectWhatsApp() {
    if (!confirm('Anda yakin ingin memutuskan koneksi WhatsApp?')) return;
    
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
            console.log('Disconnect success, waiting for status update...');
        } else {
            alert('Gagal memutuskan koneksi: ' + (data.message || 'Unknown error'));
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memutuskan koneksi');
        location.reload();
    });
}

// Auto refresh status setiap 5 detik
statusCheckInterval = setInterval(() => {
    fetch('/api/whatsapp/status?t=' + new Date().getTime())
        .then(response => response.json())
        .then(data => {
            console.log('Status check:', data); // Debug log
            if (data.success && data.account) {
                const account = data.account;
                
                // Jika status berubah, reload page
                const currentStatus = '{{ $account['status'] ?? '' }}';
                
                // Debug status comparison
                console.log(`Current: "${currentStatus}", New: "${account.status}"`);

                if (currentStatus !== account.status) {
                    console.log('Status changed. Reloading...');
                    location.reload();
                    return;
                }
                
                // Update QR jika ada (tanpa reload jika status sama tapi QR berubah)
                if (account.status === 'QR' && account.lastQr) {
                    if (account.lastQr !== qrData) {
                        console.log('New QR received');
                        qrData = account.lastQr;
                        showQRCode(qrData);
                    }
                    
                    // Jika kita sedang dalam mode loading (karena user baru klik start),
                    // tapi status sudah QR, kita harus reload atau tampilkan QR
                    const connectionStatus = document.getElementById('connectionStatus');
                    if (connectionStatus && connectionStatus.innerHTML.includes('Loading...')) {
                        console.log('Stuck in loading but status is QR. Reloading...');
                        location.reload();
                    }
                }
            }
        })
        .catch(error => console.error('Status check error:', error));
}, 3000); // Percepat interval jadi 3 detik

// Check if QRCode library is loaded
if (typeof QRCode === 'undefined') {
    console.error('QRCode library not loaded!');
    alert('Library QR Code gagal dimuat. Silakan refresh halaman.');
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (statusCheckInterval) {
        clearInterval(statusCheckInterval);
    }
});
</script>
@endpush
