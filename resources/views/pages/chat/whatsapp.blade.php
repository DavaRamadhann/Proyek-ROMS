@extends('layouts.app')

@section('title', 'Manajemen Koneksi WhatsApp')

@push('scripts')
    {{-- Kita butuh library untuk generate QR code dari string --}}
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Manajemen Koneksi WhatsApp</h5>
                    <p class="mb-0 text-muted">Hubungkan nomor WhatsApp bisnis resmi Anda.</p>
                </div>
                
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <span class="badge fs-6" id="status-badge">Memeriksa...</span>
                    </div>

                    <div id="qr-container" class="d-none">
                        <p class="text-muted">Pindai QR code ini dengan aplikasi WhatsApp di HP Anda.</p>
                        <div id="qrcode" class="d-flex justify-content-center mb-3"></div>
                        <small class="text-danger">Jangan refresh halaman ini saat memindai.</small>
                    </div>

                    <div id="message-container">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p id="message-text" class="mt-2">Sedang mengambil status koneksi...</p>
                    </div>

                    <button id="reconnect-button" class="btn btn-primary mt-3 d-none">
                        <i class="bi bi-arrow-clockwise"></i> Hubungkan Ulang (Paksa Ambil QR)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ambil elemen UI
        const statusBadge = document.getElementById('status-badge');
        const qrContainer = document.getElementById('qr-container');
        const qrCodeEl = document.getElementById('qrcode');
        const messageContainer = document.getElementById('message-container');
        const messageText = document.getElementById('message-text');
        const reconnectButton = document.getElementById('reconnect-button');

        // Ambil URL API dari route yang sudah kita buat di web.php
        const API_URL = {
            status: '{{ route('admin.whatsapp.api.status') }}',
            qr: '{{ route('admin.whatsapp.api.qr') }}',
            reconnect: '{{ route('admin.whatsapp.api.reconnect') }}'
        };

        const CSRF_TOKEN = '{{ csrf_token() }}';
        let qrCodeInstance = null; // Untuk menyimpan instance QR
        let statusInterval; // Polling untuk status
        let qrInterval; // Polling untuk QR

        /**
         * Menghentikan semua interval polling
         */
        function stopAllIntervals() {
            clearInterval(statusInterval);
            clearInterval(qrInterval);
        }

        /**
         * [MAIN FUNCTION] Memeriksa status koneksi ke whatsapp-service
         */
        async function checkStatus() {
            try {
                const response = await fetch(API_URL.status);
                if (!response.ok) {
                    updateUI('ERROR', { message: 'Gagal menghubungi server. Coba lagi.' });
                    return;
                }
                const data = await response.json();
                updateUI(data.status, data);
            } catch (error) {
                console.error('Error checkStatus:', error);
                updateUI('ERROR', { message: 'Koneksi ke ROMS server gagal.' });
            }
        }

        /**
         * [ACTION] Meminta QR code baru secara paksa
         */
        async function requestReconnect() {
            stopAllIntervals();
            updateUI('LOADING', { message: 'Meminta QR code baru...' });
            
            try {
                const response = await fetch(API_URL.reconnect, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                });

                if (!response.ok) {
                    updateUI('ERROR', { message: 'Gagal meminta reconnect. Periksa service WA.' });
                    return;
                }
                
                // Setelah berhasil request, service akan masuk ke status 'QR'
                // Kita langsung panggil checkStatus untuk konfirmasi
                checkStatus();

            } catch (error) {
                console.error('Error requestReconnect:', error);
                updateUI('ERROR', { message: 'Gagal mengirim permintaan reconnect.' });
            }
        }

        /**
         * [HELPER] Mengambil dan menampilkan QR code
         */
        async function getQrCode() {
            try {
                const response = await fetch(API_URL.qr);
                if (response.status === 404) {
                    // Ini berarti QR sudah tidak ada, kemungkinan sudah 'READY'
                    checkStatus(); // Cek status lagi
                    return;
                }
                
                const data = await response.json();
                
                // Render QR code
                if (qrCodeInstance) {
                    qrCodeInstance.clear(); // Hapus QR lama
                    qrCodeInstance.makeCode(data.qr); // Buat yang baru
                } else {
                    qrCodeInstance = new QRCode(qrCodeEl, {
                        text: data.qr,
                        width: 256,
                        height: 256,
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }
                
            } catch (error) {
                console.error('Error getQrCode:', error);
                // Jika gagal ambil QR, cek status lagi
                checkStatus();
            }
        }

        /**
         * [UI LOGIC] Mengubah tampilan berdasarkan status
         */
        function updateUI(status, data = {}) {
            stopAllIntervals(); // Hentikan polling lama sebelum set yang baru
            
            // Sembunyikan semua elemen dinamis
            qrContainer.classList.add('d-none');
            messageContainer.classList.add('d-none');
            reconnectButton.classList.add('d-none');

            switch (status) {
                case 'READY':
                    statusBadge.className = 'badge fs-6 bg-success';
                    statusBadge.textContent = 'TERHUBUNG';
                    messageContainer.classList.remove('d-none');
                    messageText.textContent = 'Koneksi WhatsApp Aktif. Sistem siap melayani chat.';
                    reconnectButton.classList.remove('d-none'); // Tetap tampilkan tombol reconnect
                    break;
                
                case 'QR':
                    statusBadge.className = 'badge fs-6 bg-warning text-dark';
                    statusBadge.textContent = 'MENUNGGU PINDAI QR';
                    qrContainer.classList.remove('d-none');
                    reconnectButton.classList.remove('d-none');
                    
                    // Ambil QR code pertama kali
                    getQrCode();
                    
                    // Polling QR setiap 10 detik (QR code biasanya refresh)
                    qrInterval = setInterval(getQrCode, 10000);
                    // Polling status setiap 2 detik (untuk cek kapan jadi 'READY')
                    statusInterval = setInterval(checkStatus, 2000);
                    break;

                case 'DISCONNECTED':
                case 'AUTH_FAILURE':
                case 'SERVICE_DOWN':
                case 'UNKNOWN':
                    statusBadge.className = 'badge fs-6 bg-danger';
                    statusBadge.textContent = 'TERPUTUS';
                    messageContainer.classList.remove('d-none');
                    messageText.textContent = 'Koneksi WhatsApp terputus. Klik tombol di bawah untuk menghubungkan.';
                    reconnectButton.classList.remove('d-none');
                    break;
                
                case 'LOADING':
                    statusBadge.className = 'badge fs-6 bg-secondary';
                    statusBadge.textContent = 'MEMPROSES...';
                    messageContainer.classList.remove('d-none');
                    messageText.textContent = data.message || 'Sedang memuat...';
                    break;

                default: // Termasuk ERROR
                    statusBadge.className = 'badge fs-6 bg-danger';
                    statusBadge.textContent = 'ERROR';
                    messageContainer.classList.remove('d-none');
                    messageText.textContent = data.message || 'Terjadi kesalahan tidak diketahui.';
                    reconnectButton.classList.remove('d-none');
            }
        }

        // --- Inisialisasi ---
        reconnectButton.addEventListener('click', requestReconnect);
        checkStatus(); // Mulai pemeriksaan saat halaman dimuat
    });
</script>
@endsection