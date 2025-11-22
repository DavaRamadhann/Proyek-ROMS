@extends('layout.cs_main') {{-- Tetap pakai layout CS --}}

@section('title', 'Riwayat Pesanan - ROMS')

@push('styles')
<style>
    .dashboard-header {
        font-size: 1.75rem;
        font-weight: 700;
        color: #333;
    }
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    /* Tombol Aksi */
    .btn-aksi {
        font-size: 0.85rem;
        padding: 0.25rem 0.5rem;
    }

    /* Kustomisasi Badge Status Pesanan (sesuai palet) */
    .badge-custom-success {
        background-color: #84994F; /* Hijau (Selesai/Dikirim) */
        color: white;
    }
    .badge-custom-warning {
        background-color: #FCB53B; /* Emas (Pending) */
        color: #333; 
    }
    .badge-custom-danger {
        background-color: #B45253; /* Maroon (Batal) */
        color: white;
    }
</style>
@endpush


@section('main-content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="dashboard-header">Riwayat Pesanan</h2>
    <div class="input-group" style="max-width: 400px;">
        <input type="text" class="form-control" placeholder="Cari Order ID, Nama Pelanggan...">
        <button class="btn btn-outline-secondary" type="button">
            <i class="bi bi-search"></i>
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Order ID</th>
                    <th scope="col">Nama Pelanggan</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Total</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data Dummy (Contoh) --}}
                <tr>
                    <td><strong>#12345</strong></td>
                    <td>Ahmad Subagja</td>
                    <td>10 Nov 2025</td>
                    <td>Rp 150.000</td>
                    <td>
                        <span class="badge badge-custom-success">Dalam Pengiriman</span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-outline-primary btn-aksi">
                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                        </a>
                        <a href="/cs-pesanan/detail" class="btn btn-outline-secondary btn-aksi">
                            <i class="bi bi-eye-fill me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><strong>#12344</strong></td>
                    <td>Siti Lestari</td>
                    <td>10 Nov 2025</td>
                    <td>Rp 85.000</td>
                    <td>
                        <span class="badge badge-custom-warning">Pending</span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-outline-primary btn-aksi">
                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                        </a>
                        <a href="/cs-pesanan/detail" class="btn btn-outline-secondary btn-aksi">
                            <i class="bi bi-eye-fill me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><strong>#12001</strong></td>
                    <td>Ahmad Subagja</td>
                    <td>01 Okt 2025</td>
                    <td>Rp 85.000</td>
                    <td>
                        <span class="badge badge-custom-success">Selesai</span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-outline-primary btn-aksi">
                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                        </a>
                        <a href="/cs-pesanan/detail" class="btn btn-outline-secondary btn-aksi">
                            <i class="bi bi-eye-fill me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><strong>#11950</strong></td>
                    <td>Budi Hartono</td>
                    <td>28 Sep 2025</td>
                    <td>Rp 210.000</td>
                    <td>
                        <span class="badge badge-custom-danger">Dibatalkan</span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-outline-primary btn-aksi">
                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                        </a>
                        <a href="/cs-pesanan/detail" class="btn btn-outline-secondary btn-aksi">
                            <i class="bi bi-eye-fill me-1"></i> Detail
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection