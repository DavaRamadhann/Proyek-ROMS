@extends('layout.main') {{-- Tetap pakai layout utama --}}

@section('title', 'Otomasi Pesan - ROMS')

{{-- Style khusus untuk halaman ini --}}
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
        color: #333; /* Teks gelap agar terbaca */
    }
    .badge-custom-danger {
        background-color: #B45253; /* Maroon */
        color: white;
    }
</style>
@endpush


@section('main-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="dashboard-header">Dashboard Otomasi Pesan</h2>
    <a href="/otomasi-pesan/tambah" class="btn btn-gold">
        <i class="bi bi-plus-circle-fill me-1"></i>
        Tambah Otomasi Pesan
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Nama Pelanggan</th>
                    <th scope="col">Produk</th>
                    <th scope="col">Tanggal Pesan Dikirim</th>
                    <th scope="col">Status Pesan</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data Dummy (Contoh) --}}
                <tr>
                    <td>Ahmad Subagja</td>
                    <td>Kemeja Lengan Panjang</td>
                    <td>10 Nov 2025, 10:30</td>
                    <td>
                        <span class="badge badge-custom-success">Terkirim</span>
                    </td>
                </tr>
                <tr>
                    <td>Siti Lestari</td>
                    <td>Gamis Wanita Mocca</td>
                    <td>11 Nov 2025, 11:00</td>
                    <td>
                        <span class="badge badge-custom-warning">Tertunda</span>
                    </td>
                </tr>
                <tr>
                    <td>Budi Hartono</td>
                    <td>Celana Chino Pria</td>
                    <td>11 Nov 2025, 09:15</td>
                    <td>
                        <span class="badge badge-custom-danger">Gagal</span>
                    </td>
                </tr>
                <tr>
                    <td>Dewi Anggraini</td>
                    <td>Sepatu Sneakers Putih</td>
                    <td>12 Nov 2025, 14:00</td>
                    <td>
                        <span class="badge badge-custom-warning">Tertunda</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection