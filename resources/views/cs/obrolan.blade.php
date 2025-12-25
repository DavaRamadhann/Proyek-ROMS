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

<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.4/dist/index.min.js"></script>
@endsection