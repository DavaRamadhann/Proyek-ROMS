@extends('layout.cs_main')

@section('title', 'Beranda CS - ROMS')

@push('styles')
<style>
    .dashboard-header {
        font-size: 1.75rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 1.5rem;
    }
    .info-card {
        border: none;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        height: 100%;
    }
    .info-card:hover {
        transform: translateY(-5px);
    }
    .info-card .card-body {
        padding: 1.5rem;
    }
    .info-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0.5rem 0;
        color: #333;
    }
    .text-maroon { color: #B45253 !important; }
    .text-green { color: #84994F !important; }
    .text-gold { color: #FCB53B !important; }
    
    .table-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .table-header {
        background-color: #fff;
        padding: 1.5rem;
        border-bottom: 1px solid #f0f0f0;
    }
</style>
@endpush

@section('main-content')

<h2 class="dashboard-header">Halo, Customer Service! 👋</h2>

<div class="row g-4 mb-5">
    <div class="col-lg-4 col-md-6">
        <div class="card info-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="info-label">Obrolan Menunggu</span>
                        <h3 class="info-value text-maroon">5</h3>
                        <small class="text-muted">Perlu respon segera</small>
                    </div>
                    <div class="fs-1 text-maroon opacity-25">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card info-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="info-label">Pesanan Baru</span>
                        <h3 class="info-value text-gold">12</h3>
                        <small class="text-muted">Menunggu konfirmasi</small>
                    </div>
                    <div class="fs-1 text-gold opacity-25">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-12">
        <div class="card info-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="info-label">Pelanggan Aktif</span>
                        <h3 class="info-value text-green">150</h3>
                        <small class="text-muted">Total database pelanggan</small>
                    </div>
                    <div class="fs-1 text-green opacity-25">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card table-card">
    <div class="table-header d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0">Aktivitas Terkini</h5>
        <a href="{{ route('chat.whatsapp') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua Obrolan</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3">Aktivitas</th>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3 text-end">Status</th>
                </tr>
            </thead>
            <tbody>
                {{-- Dummy Data --}}
                <tr>
                    <td class="px-4"><span class="fw-bold">Ahmad Subagja</span></td>
                    <td class="px-4 text-muted">Mengirim pesan baru</td>
                    <td class="px-4">10:30 WIB</td>
                    <td class="px-4 text-end"><span class="badge bg-danger">Belum Dibaca</span></td>
                </tr>
                <tr>
                    <td class="px-4"><span class="fw-bold">Siti Lestari</span></td>
                    <td class="px-4 text-muted">Pesanan #ORDER-123 selesai</td>
                    <td class="px-4">09:15 WIB</td>
                    <td class="px-4 text-end"><span class="badge bg-success">Selesai</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection