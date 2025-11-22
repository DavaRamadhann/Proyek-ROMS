@extends('layout.main')

@section('title', 'Dashboard - ROMS')

@section('search-placeholder', 'Cari menu atau fitur...')

@section('topbar-actions')
<button class="btn btn-primary" onclick="window.print()">
    <i class="bi bi-printer me-1"></i><span class="d-none d-lg-inline">Cetak Laporan</span>
</button>
@endsection

@push('styles')
<style>
    .dashboard-header {
        font-size: 1.75rem;
        font-weight: 700;
        color: #333;
    }
    .welcome-card {
        background: linear-gradient(135deg, #84994F 0%, #6b7d3f 100%);
        color: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .module-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    .module-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .module-card .card-body {
        padding: 25px;
    }
    .module-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    .badge-role {
        background-color: #FCB53B;
        color: #333;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 10px 0;
    }
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
    }
</style>
@endpush

@section('main-content')

<h2 class="dashboard-header mb-4">Dashboard</h2>

<div class="welcome-card">
    <h3 class="mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h3>
    <p class="mb-0">Anda login sebagai: <span class="badge-role">{{ strtoupper(auth()->user()->role) }}</span></p>
    <small class="opacity-75">Sistem manajemen pesanan dan pengiriman ROMS</small>
</div>

{{-- Statistik Cepat --}}
@if(in_array(auth()->user()->role, ['admin', 'cs']))
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #0d6efd;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Chat Aktif</div>
                    <div class="stat-value text-primary">0</div>
                    <small class="text-muted">Obrolan hari ini</small>
                </div>
                <i class="bi bi-chat-dots-fill text-primary" style="font-size: 2rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #84994F;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Pelanggan</div>
                    <div class="stat-value" style="color: #84994F;">0</div>
                    <small class="text-muted">Data pelanggan</small>
                </div>
                <i class="bi bi-people-fill" style="font-size: 2rem; opacity: 0.3; color: #84994F;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #FCB53B;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Produk</div>
                    <div class="stat-value" style="color: #FCB53B;">0</div>
                    <small class="text-muted">SKU tersedia</small>
                </div>
                <i class="bi bi-box-seam-fill" style="font-size: 2rem; opacity: 0.3; color: #FCB53B;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #B45253;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Pesanan Hari Ini</div>
                    <div class="stat-value" style="color: #B45253;">0</div>
                    <small class="text-muted">Order masuk</small>
                </div>
                <i class="bi bi-receipt-cutoff" style="font-size: 2rem; opacity: 0.3; color: #B45253;"></i>
            </div>
        </div>
    </div>
</div>
@endif

<h4 class="mb-3 fw-bold" style="color: #333;">
    <i class="bi bi-grid-3x3-gap-fill me-2"></i>Akses Cepat
</h4>

<div class="row g-4">
    {{-- 1. Kartu Chat CS --}}
    @if(in_array(auth()->user()->role, ['admin', 'cs']))
    <div class="col-md-6 col-lg-3">
        <div class="module-card card">
            <div class="card-body text-center">
                <div class="module-icon text-primary">
                    <i class="bi bi-chat-dots-fill"></i>
                </div>
                <h5 class="card-title fw-bold">Chat CS</h5>
                <p class="card-text text-muted">Buka inbox untuk melihat dan membalas pesan pelanggan.</p>
                <a href="{{ route('chat.index') }}" class="btn btn-primary btn-sm">Buka Inbox</a>
            </div>
        </div>
    </div>
    @endif

    {{-- 2. Kartu Manajemen Pelanggan --}}
    @if(in_array(auth()->user()->role, ['admin', 'cs']))
    <div class="col-md-6 col-lg-3">
        <div class="module-card card">
            <div class="card-body text-center">
                <div class="module-icon" style="color: #84994F;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h5 class="card-title fw-bold">Pelanggan</h5>
                <p class="card-text text-muted">Kelola data master pelanggan, riwayat, dan segmen.</p>
                <a href="{{ route('customer.index') }}" class="btn btn-sm" style="background-color: #84994F; color: white;">Kelola Pelanggan</a>
            </div>
        </div>
    </div>
    @endif

    {{-- 3. Kartu Manajemen Produk --}}
    @if(in_array(auth()->user()->role, ['admin', 'cs']))
    <div class="col-md-6 col-lg-3">
        <div class="module-card card">
            <div class="card-body text-center">
                <div class="module-icon" style="color: #FCB53B;">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <h5 class="card-title fw-bold">Produk</h5>
                <p class="card-text text-muted">Kelola data produk, kode SKU, dan inventaris.</p>
                <a href="{{ route('product.index') }}" class="btn btn-sm" style="background-color: #FCB53B; color: #333;">Kelola Produk</a>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. Kartu Pesanan --}}
    @if(in_array(auth()->user()->role, ['admin', 'cs']))
    <div class="col-md-6 col-lg-3">
        <div class="module-card card">
            <div class="card-body text-center">
                <div class="module-icon" style="color: #B45253;">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <h5 class="card-title fw-bold">Pesanan</h5>
                <p class="card-text text-muted">Kelola pesanan pelanggan dan tracking pengiriman.</p>
                <a href="{{ route('order.index') }}" class="btn btn-sm" style="background-color: #B45253; color: white;">Kelola Pesanan</a>
            </div>
        </div>
    </div>
    @endif

    {{-- 5. Admin Dashboard --}}
    @if(auth()->user()->role === 'admin')
    <div class="col-md-6 col-lg-3">
        <div class="module-card card">
            <div class="card-body text-center">
                <div class="module-icon text-success">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <h5 class="card-title fw-bold">Admin Dashboard</h5>
                <p class="card-text text-muted">Dashboard khusus admin dengan analisis lengkap.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-success btn-sm">Buka Dashboard</a>
            </div>
        </div>
    </div>
    @endif

    {{-- 6. CS Dashboard --}}
    @if(in_array(auth()->user()->role, ['admin', 'cs']))
    <div class="col-md-6 col-lg-3">
        <div class="module-card card">
            <div class="card-body text-center">
                <div class="module-icon text-info">
                    <i class="bi bi-headset"></i>
                </div>
                <h5 class="card-title fw-bold">CS Dashboard</h5>
                <p class="card-text text-muted">Dashboard customer service untuk layanan pelanggan.</p>
                <a href="{{ route('chat.dashboard') }}" class="btn btn-info btn-sm">Buka Dashboard</a>
            </div>
        </div>
    </div>
    @endif

    {{-- 7. Otomasi Pesan --}}
    @if(auth()->user()->role === 'admin')
    <div class="col-md-6 col-lg-3">
        <div class="module-card card">
            <div class="card-body text-center">
                <div class="module-icon text-info">
                    <i class="bi bi-send-fill"></i>
                </div>
                <h5 class="card-title fw-bold">Otomasi Pesan</h5>
                <p class="card-text text-muted">Pengaturan otomasi broadcast pesan WhatsApp.</p>
                <a href="{{ route('admin.otomasi-pesan') }}" class="btn btn-info btn-sm">Kelola Otomasi</a>
            </div>
        </div>
    </div>
    @endif

    {{-- 8. Reminder Rules --}}
    @if(auth()->user()->role === 'admin')
    <div class="col-md-6 col-lg-3">
        <div class="module-card card">
            <div class="card-body text-center">
                <div class="module-icon text-warning">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <h5 class="card-title fw-bold">Reminder</h5>
                <p class="card-text text-muted">Aturan reminder otomatis untuk repeat order.</p>
                <a href="{{ route('reminder.index') }}" class="btn btn-warning btn-sm">Kelola Reminder</a>
            </div>
        </div>
    </div>
    @endif

    {{-- 9. Daftar Acara --}}
    @if(auth()->user()->role === 'admin')
    <div class="col-md-6 col-lg-3">
        <div class="module-card card">
            <div class="card-body text-center">
                <div class="module-icon" style="color: #B45253;">
                    <i class="bi bi-megaphone-fill"></i>
                </div>
                <h5 class="card-title fw-bold">Daftar Acara</h5>
                <p class="card-text text-muted">Kelola acara dan kampanye marketing.</p>
                <a href="{{ route('admin.daftar-acara') }}" class="btn btn-sm" style="background-color: #B45253; color: white;">Kelola Acara</a>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- Pintasan Cepat (Quick Actions) --}}
@if(in_array(auth()->user()->role, ['admin', 'cs']))
<div class="row mt-5">
    <div class="col-12">
        <h4 class="mb-3 fw-bold" style="color: #333;">
            <i class="bi bi-lightning-charge-fill me-2"></i>Pintasan Cepat
        </h4>
    </div>
</div>

<div class="row g-3">
    {{-- Tambah Pelanggan Baru --}}
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('customer.create') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3" style="background-color: rgba(132, 153, 79, 0.1);">
                        <i class="bi bi-person-plus-fill" style="font-size: 1.5rem; color: #84994F;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Tambah Pelanggan</h6>
                        <small class="text-muted">Daftarkan pelanggan baru</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Tambah Produk Baru --}}
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('product.create') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3" style="background-color: rgba(252, 181, 59, 0.1);">
                        <i class="bi bi-box-seam" style="font-size: 1.5rem; color: #FCB53B;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Tambah Produk</h6>
                        <small class="text-muted">Input produk baru</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Buat Pesanan Baru --}}
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('order.create') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3" style="background-color: rgba(180, 82, 83, 0.1);">
                        <i class="bi bi-cart-plus-fill" style="font-size: 1.5rem; color: #B45253;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Buat Pesanan</h6>
                        <small class="text-muted">Order baru</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    @if(auth()->user()->role === 'admin')
    {{-- Tambah Reminder --}}
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('reminder.create') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3" style="background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-alarm" style="font-size: 1.5rem; color: #ffc107;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Atur Reminder</h6>
                        <small class="text-muted">Reminder repeat order</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Tambah Acara --}}
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('admin.daftar-acara.tambah') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3" style="background-color: rgba(180, 82, 83, 0.1);">
                        <i class="bi bi-calendar-event" style="font-size: 1.5rem; color: #B45253;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Tambah Acara</h6>
                        <small class="text-muted">Buat acara/kampanye</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Tambah Otomasi --}}
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('admin.otomasi-pesan.tambah') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3" style="background-color: rgba(13, 110, 253, 0.1);">
                        <i class="bi bi-robot" style="font-size: 1.5rem; color: #0d6efd;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Tambah Otomasi</h6>
                        <small class="text-muted">Broadcast WhatsApp</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
</div>
@endif

@push('styles')
<style>
    .hover-card {
        transition: all 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    }
</style>
@endpush

@endsection