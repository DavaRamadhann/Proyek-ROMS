@extends('layout.cs_main')

@section('title', 'Detail Pelanggan - Ahmad Subagja')

@push('styles')
<style>
    /* Palet Warna */
    :root {
        --color-maroon: #B45253;
        --color-gold: #FCB53B;
        --color-green: #84994F;
    }

    .dashboard-header { font-size: 1.5rem; font-weight: 700; color: #333; }
    
    .card {
        border-radius: 12px; border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px;
    }
    .card-header {
        background-color: white; border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px; font-weight: 700;
    }

    /* Profil Style */
    .profile-header {
        text-align: center; padding: 30px 20px;
        background: linear-gradient(to bottom, #f8f9fa 50%, white 50%);
    }
    .profile-avatar {
        width: 100px; height: 100px; border-radius: 50%;
        background-color: var(--color-maroon); color: white;
        font-size: 2.5rem; font-weight: bold;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 15px; border: 4px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Info Label */
    .info-label { font-size: 0.85rem; color: #6c757d; margin-bottom: 2px; }
    .info-value { font-weight: 600; color: #333; margin-bottom: 15px; }

    /* Stat Cards Kecil */
    .stat-card {
        background-color: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center;
    }
    .stat-value { font-size: 1.25rem; font-weight: 700; color: var(--color-green); }
    .stat-label { font-size: 0.8rem; color: #6c757d; }

    /* Badge Segmen */
    .badge-loyal { background-color: var(--color-green); color: white; }
</style>
@endpush

@section('main-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="/cs-pelanggan" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left"></i> Kembali ke Data Pelanggan
        </a>
        <h2 class="dashboard-header mt-1">Profil Pelanggan</h2>
    </div>
    <div>
        <button class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-pencil"></i> Edit Profil</button>
        <a href="/cs-obrolan" class="btn btn-primary btn-sm" style="background-color: #B45253; border-color: #B45253;">
            <i class="bi bi-chat-dots-fill me-1"></i> Chat Pelanggan
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="profile-header">
                <div class="profile-avatar">AS</div>
                <h5 class="fw-bold mb-1">Ahmad Subagja</h5>
                <span class="badge badge-loyal rounded-pill px-3 mb-2">Pelanggan Loyal</span>
                <div class="text-muted small">Bergabung sejak 01 Okt 2025</div>
            </div>
            <div class="card-body pt-0">
                <hr>
                <div class="mb-3">
                    <div class="info-label">Email</div>
                    <div class="info-value">ahmad.subagja@dummy.com</div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Nomor Telepon</div>
                    <div class="info-value">0812-3456-7890 <i class="bi bi-whatsapp text-success ms-1"></i></div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Alamat Utama</div>
                    <div class="info-value">
                        Jl. Kopi No. 1, RT 05/RW 02<br>
                        Kec. Kebayoran Baru, Jakarta Selatan<br>
                        DKI Jakarta, 12150
                    </div>
                </div>
                <div class="mb-0">
                    <div class="info-label">Catatan Internal</div>
                    <div class="alert alert-warning small mb-0 p-2">
                        <i class="bi bi-info-circle me-1"></i> Pelanggan suka produk kopi arabika. Respon cepat di WA.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-value">Rp 5.400.000</div>
                    <div class="stat-label">Total Belanja (Lifetime)</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-value">24</div>
                    <div class="stat-label">Total Pesanan</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-value">Rp 225.000</div>
                    <div class="stat-label">Rata-rata Order</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Riwayat Pesanan</span>
                <button class="btn btn-sm btn-link text-decoration-none text-muted">Lihat Semua</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">ID Order</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4 fw-bold">#12345</td>
                                <td>10 Nov 2025</td>
                                <td><span class="badge bg-success">Selesai</span></td>
                                <td>Rp 150.000</td>
                                <td class="text-end pe-4">
                                    <a href="/cs-pesanan/detail" class="btn btn-sm btn-outline-secondary">Detail</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-bold">#12300</td>
                                <td>25 Okt 2025</td>
                                <td><span class="badge bg-success">Selesai</span></td>
                                <td>Rp 350.000</td>
                                <td class="text-end pe-4">
                                    <a href="/cs-pesanan/detail" class="btn btn-sm btn-outline-secondary">Detail</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-bold">#12150</td>
                                <td>10 Okt 2025</td>
                                <td><span class="badge bg-danger">Dibatalkan</span></td>
                                <td>Rp 85.000</td>
                                <td class="text-end pe-4">
                                    <a href="/cs-pesanan/detail" class="btn btn-sm btn-outline-secondary">Detail</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Produk Sering Dibeli</div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-light text-dark border p-2">☕ Kopi Arabika Gayo (10x)</span>
                    <span class="badge bg-light text-dark border p-2">🫘 Robusta Lampung (5x)</span>
                    <span class="badge bg-light text-dark border p-2">📄 Filter V60 (3x)</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection