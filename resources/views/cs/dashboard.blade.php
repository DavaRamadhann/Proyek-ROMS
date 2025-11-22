@extends('layout.cs_main') {{-- PENTING: Menggunakan layout CS baru --}}

@section('title', 'Beranda CS - ROMS')

@push('styles')
<style>
    /* Style ini mirip dengan dashboard admin, tapi pakai palet Anda */
    .dashboard-header {
        font-size: 1.75rem;
        font-weight: 700;
        color: #333;
    }
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        height: 100%; 
    }
    .info-card .info-value {
        font-size: 1.75rem;
        font-weight: 700;
    }
    .saldo-label {
        font-size: 0.9rem;
        color: #6c757d;
    }
</style>
@endpush


@section('main-content')

<h2 class="dashboard-header mb-4">Dashboard Customer Service</h2>

<div class="row g-4">
    <div class="col-lg-4 col-md-6">
        <div class="card p-3 info-card">
            <span class="saldo-label">Obrolan Belum Dibaca</span>
            <h3 class="info-value text-danger">5</h3>
            <small class="text-muted">Dari 20 total obrolan hari ini</small>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card p-3 info-card">
            <span class="saldo-label">Pesanan Perlu Diproses</span>
            <h3 class="info-value text-warning">12</h3>
            <small class="text-muted">Pesanan baru menunggu konfirmasi</small>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
        <div class="card p-3 info-card">
            <span class="saldo-label">Total Pelanggan Aktif</span>
            <h3 class="info-value">150</h3>
            <small class="text-muted">Data statis (dummy)</small>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Obrolan Terbaru (Dummy)</h5>
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Nama Pelanggan</th>
                    <th scope="col">Pesan Terakhir</th>
                    <th scope="col">Waktu</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Ahmad Subagja</td>
                    <td>"Halo kak, pesanan saya..."</td>
                    <td>5 menit lalu</td>
                    <td><span class="badge bg-danger">Belum Dibaca</span></td>
                </tr>
                <tr>
                    <td>Siti Lestari</td>
                    <td>"Terima kasih bantuannya kak!"</td>
                    <td>2 jam lalu</td>
                    <td><span class="badge bg-success">Selesai</span></td>
                </tr>
                <tr>
                    <td>Budi Hartono</td>
                    <td>"Oke saya tunggu update resinya."</td>
                    <td>Kemarin</td>
                    <td><span class="badge bg-secondary">Sudah Dibaca</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection