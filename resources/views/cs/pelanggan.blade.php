@extends('layout.cs_main') {{-- Tetap pakai layout CS --}}

@section('title', 'Data Pelanggan - ROMS')

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

    /* Kustomisasi Badge Segmen (sesuai PDF) */
    .badge-custom-loyal {
        background-color: #84994F; /* Hijau (Loyal) */
        color: white;
    }
    .badge-custom-baru {
        background-color: #FCB53B; /* Emas (Baru) */
        color: #333; 
    }
    .badge-custom-aktif {
        background-color: #45b6e8; /* Biru (Aktif) */
        color: white;
    }
    .badge-custom-calon {
        background-color: #6c757d; /* Abu-abu (Calon) */
        color: white;
    }
</style>
@endpush


@section('main-content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="dashboard-header">Data Pelanggan</h2>
    <div class="input-group" style="max-width: 400px;">
        <input type="text" class="form-control" placeholder="Cari nama, email, atau telepon...">
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
                    <th scope="col">Nama Pelanggan</th>
                    <th scope="col">Kontak</th>
                    <th scope="col">Tanggal Bergabung</th>
                    <th scope="col">Total Pesanan</th>
                    <th scope="col">Segmen</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data Dummy (Contoh) --}}
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-2" style="background-color: #B45253; color: white;">AS</div>
                            <strong>Ahmad Subagja</strong>
                        </div>
                    </td>
                    <td>ahmad.subagja@dummy.com</td>
                    <td>01 Okt 2025</td>
                    <td>2 Pesanan</td>
                    <td>
                        <span class="badge badge-custom-loyal">Loyal</span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-outline-primary btn-aksi">
                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                        </a>
                        <a href="/cs-pelanggan/detail" class="btn btn-outline-secondary btn-aksi">
                            <i class="bi bi-eye-fill me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-2" style="background-color: #B45253; color: white;">SL</div>
                            <strong>Siti Lestari</strong>
                        </div>
                    </td>
                    <td>081298765432</td>
                    <td>10 Nov 2025</td>
                    <td>1 Pesanan</td>
                    <td>
                        <span class="badge badge-custom-baru">Baru</span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-outline-primary btn-aksi">
                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                        </a>
                        <a href="/cs-pelanggan/detail" class="btn btn-outline-secondary btn-aksi">
                            <i class="bi bi-eye-fill me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-2" style="background-color: #B45253; color: white;">BH</div>
                            <strong>Budi Hartono</strong>
                        </div>
                    </td>
                    <td>budi.hartono@dummy.com</td>
                    <td>15 Sep 2025</td>
                    <td>5 Pesanan</td>
                    <td>
                        <span class="badge badge-custom-loyal">Loyal</span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-outline-primary btn-aksi">
                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                        </a>
                        <a href="/cs-pelanggan/detail" class="btn btn-outline-secondary btn-aksi">
                            <i class="bi bi-eye-fill me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-2" style="background-color: #B45253; color: white;">DA</div>
                            <strong>Dewi Anggraini</strong>
                        </div>
                    </td>
                    <td>085611112222</td>
                    <td>05 Nov 2025</td>
                    <td>3 Pesanan</td>
                    <td>
                        <span class="badge badge-custom-aktif">Aktif</span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-outline-primary btn-aksi">
                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                        </a>
                        <a href="/cs-pelanggan/detail" class="btn btn-outline-secondary btn-aksi">
                            <i class="bi bi-eye-fill me-1"></i> Detail
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

{{-- Helper CSS untuk Avatar Kecil --}}
@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
    flex-shrink: 0;
}
</style>
@endpush