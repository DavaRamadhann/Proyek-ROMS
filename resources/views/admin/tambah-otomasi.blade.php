@extends('layout.main')

@section('title', 'Tambah Otomasi - ROMS')

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
    
    /* Tombol Aksi (sesuai palet) */
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

    /* Kustomisasi Form */
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
    <h2 class="dashboard-header">Tambah Otomasi Pesan Baru</h2>
    <a href="/otomasi-pesan" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Kembali ke Daftar
    </a>
</div>

<div class="card">
    <div class="card-body p-4 p-md-5">
        
        <form action="#" method="GET">
            <div class="row g-3">
                
                {{-- FIELD JUDUL SUDAH DIHAPUS --}}

                <div class="col-12">
                    <label for="nama_pesan" class="form-label">Nama Pesan</label>
                    <input type="text" class="form-control" id="nama_pesan" placeholder="cth: Hai Kak! Order yuk!" required>
                </div>

                <div class="col-md-6">
                    <label for="tipe_pesan" class="form-label">Tipe Pesan</label>
                    <select class="form-select" id="tipe_pesan" required>
                        <option value="" selected disabled>-- Pilih Tipe Pesan --</option>
                        <option value="reminder">Pengingat Repeat Order</option>
                        <option value="thanks">Ucapan Terima Kasih</option>
                        <option value="promo">Promosi Produk</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="waktu_pemicu" class="form-label">Waktu Pemicu</label>
                    <input type="datetime-local" class="form-control" id="waktu_pemicu" required>
                    <div class="form-text">Kapan pesan ini akan dikirimkan.</div>
                </div>

                <div class="col-12">
                    <label for="isi_pesan" class="form-label">Isi Pesan</label>
                    <textarea class="form-control" id="isi_pesan" rows="6" placeholder="Tulis isi pesan Anda di sini..." required></textarea>
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