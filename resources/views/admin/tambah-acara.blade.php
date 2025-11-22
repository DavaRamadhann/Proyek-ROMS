@extends('layout.main')

@section('title', 'Tambah Acara - ROMS')

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
    .btn-maroon {
        background-color: #B45253; /* Maroon */
        border-color: #B45253;
        color: white;
        font-weight: 600;
    }
    .btn-maroon:hover {
        background-color: #9a4243;
        border-color: #9a4243;
        color: white;
    }
    .form-label {
        font-weight: 600;
    }
    .form-control:focus, .form-select:focus {
        border-color: #FCB53B; /* Emas */
        box-shadow: 0 0 0 0.2rem rgba(252, 181, 59, 0.25);
    }
</style>
@endpush

@section('main-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="dashboard-header">Tambah Acara Baru</h2>
    <a href="/daftar-acara" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Kembali ke Daftar
    </a>
</div>

<div class="card">
    <div class="card-body p-4 p-md-5">
        
        <form action="#" method="GET">
            {{-- 
            =================================================
            LAYOUT FORM BARU SESUAI PERMINTAAN ANDA
            =================================================
            --}}
            <div class="row g-3">
                
                <div class="col-md-6">
                    <label for="nama_acara" class="form-label">Nama Acara</label>
                    <input type="text" class="form-control" id="nama_acara" placeholder="cth: Promo Akhir Tahun" required>
                </div>

                <div class="col-md-6">
                    <label for="jenis_acara" class="form-label">Jenis Acara</label>
                    <select class="form-select" id="jenis_acara" required>
                        <option value="" selected disabled>-- Pilih Jenis Acara --</option>
                        <option value="produk_baru">Produk Baru</option>
                        <option value="diskon">Diskon</option>
                        <option value="hari_besar">Ucapan Hari Besar</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="target_audiens" class="form-label">Target Audiens</label>
                    <select class="form-select" id="target_audiens" required>
                        <option value="" selected disabled>-- Pilih Target Audiens --</option>
                        <option value="aktif">Pelanggan Aktif</option>
                        <option value="baru">Pelanggan Baru</option>
                        <option value="loyal">Pelanggan Loyal</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="waktu_pemicu" class="form-label">Tanggal Pesan Dikirim</label>
                    <input type="datetime-local" class="form-control" id="waktu_pemicu" required>
                </div>

                <div class="col-12">
                    <label for="isi_pesan" class="form-label">Isi Pesan (Opsional)</label>
                    <textarea class="form-control" id="isi_pesan" rows="6" placeholder="Tulis isi pesan Anda di sini..."></textarea>
                </div>

                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-outline-secondary me-2">
                        Simpan (Draft)
                    </button>
                    <button type="submit" class="btn btn-maroon">
                        Simpan dan Aktifkan
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

@endsection