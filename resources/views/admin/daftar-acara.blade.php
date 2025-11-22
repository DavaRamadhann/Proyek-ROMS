@extends('layout.main')

@section('title', 'Daftar Acara - ROMS')

@push('styles')
<style>
    .dashboard-header {
        font-size: 1.75rem;
        font-weight: 700;
        color: #333;
    }

    /* Tombol Tambah (Warna Emas) */
    .btn-gold {
        background-color: #FCB53B;
        border-color: #FCB53B;
        color: white;
        font-weight: 600;
    }
    .btn-gold:hover {
        background-color: #e0a436;
        border-color: #e0a436;
        color: white;
    }

    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    /* Kustomisasi Badge Status (sesuai palet) */
    .badge-custom-success {
        background-color: #84994F; /* Hijau */
        color: white;
    }
    .badge-custom-warning {
        background-color: #FCB53B; /* Emas */
        color: #333; 
    }
    .badge-custom-danger {
        background-color: #B45253; /* Maroon */
        color: white;
    }
    
    /* Kustomisasi Badge Audiens */
    .badge-custom-primary {
        background-color: #6658dd;
        color: white;
    }
    .badge-custom-info {
        background-color: #45b6e8;
        color: white;
    }
    .badge-custom-secondary {
        background-color: #6c757d;
        color: white;
    }
</style>
@endpush


@section('main-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="dashboard-header">Daftar Acara</h2>
    <a href="/daftar-acara/tambah" class="btn btn-gold">
        <i class="bi bi-plus-circle-fill me-1"></i>
        Tambah Acara
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Nama Acara</th>
                    <th scope="col">Tanggal Pesan Dikirim</th>
                    <th scope="col">Status</th>
                    <th scope="col">Target Audiens</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data Dummy (Contoh) --}}
                <tr>
                    <td>Promo Akhir Tahun</td>
                    <td>25 Des 2025, 10:00</td>
                    <td>
                        <span class="badge badge-custom-success">Terkirim</span>
                    </td>
                    <td>
                        <span class="badge badge-custom-primary">Pelanggan Loyal</span>
                    </td>
                </tr>
                <tr>
                    <td>Flash Sale 11.11</td>
                    <td>11 Nov 2025, 00:00</td>
                    <td>
                        <span class="badge badge-custom-warning">Terjadwalkan</span>
                    </td>
                    <td>
                        <span class="badge badge-custom-secondary">Pelanggan Aktif</span>
                    </td>
                </tr>
                <tr>
                    <td>Reminder Produk Baru</td>
                    <td>10 Nov 2025, 14:00</td>
                    <td>
                        <span class="badge badge-custom-danger">Gagal</span>
                    </td>
                    <td>
                        <span class="badge badge-custom-info">Pelanggan Baru</span>
                    </td>
                </tr>
                <tr>
                    <td>Ucapan Terima Kasih</td>
                    <td>Terkirim setelah order</td>
                    <td>
                        <span class="badge badge-custom-success">Terkirim</span>
                    </td>
                    <td>
                        <span class="badge badge-custom-info">Pelanggan Baru</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection