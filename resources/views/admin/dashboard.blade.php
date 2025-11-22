@extends('layout.main')

@section('title', 'Dashboard Admin - ROMS')

@push('styles')
<style>
    .dashboard-header {
        font-size: 1.75rem; font-weight: 700; color: #333;
    }
    .cta-banner {
        background-color: #84994F; /* HIJAU ANDA */
        color: white; border-radius: 12px;
    }
    .cta-banner a {
        color: #FCB53B; /* EMAS ANDA */
        text-decoration: none; font-weight: 600;
    }
    .card {
        border-radius: 12px; border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); height: 100%;
    }
    
    /* --- STYLE UTAMA FITUR ANALISIS --- */
    .section-title {
        font-size: 1.2rem; font-weight: 700; color: #333; 
        margin-bottom: 15px; border-left: 5px solid #FCB53B; padding-left: 10px;
    }
    .map-placeholder {
        background-color: #e9ecef; border-radius: 12px; height: 350px;
        display: flex; align-items: center; justify-content: center; color: #6c757d; font-weight: 600;
        position: relative; overflow: hidden;
    }
    
    /* Bar volume pembelian */
    .volume-bar { height: 8px; border-radius: 4px; background-color: #eee; overflow: hidden; }
    .volume-fill { height: 100%; background-color: #B45253; } /* Maroon */
    
    /* Badges Segmen */
    .badge-loyal { background-color: #84994F; color: white; } /* Hijau */
    .badge-aktif { background-color: #45b6e8; color: white; } /* Biru */
    .badge-baru { background-color: #FCB53B; color: #333; }   /* Emas */
</style>
@endpush


@section('main-content')

<h2 class="dashboard-header mb-4">Analisis</h2>

<div class="cta-banner p-4 mb-5 d-flex flex-column flex-md-row justify-content-between align-items-center">
    <div class="d-flex align-items-center mb-3 mb-md-0">
        <div style="font-size: 2rem;" class="me-3 d-none d-md-block">💡</div>
        <div>
            <strong class="d-block">Halo Admin! Selamat Datang di ROMS.</strong>
            <small>Berikut adalah ringkasan performa toko dan analisis pelanggan Anda.</small>
        </div>
    </div>
</div>


<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h4 class="section-title mb-0">Persebaran Area & Hotspot Pembelian</h4>
            <select class="form-select form-select-sm w-auto shadow-sm">
                <option>Kuartal Terakhir (Q3)</option>
                <option>Bulan Ini</option>
                <option>Tahun Ini</option>
            </select>
        </div>
    </div>

    <div class="col-lg-7 mb-3 mb-lg-0">
        <div class="card p-0 overflow-hidden">
            <div class="map-placeholder">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Indonesia_location_map.svg/2400px-Indonesia_location_map.svg.png" 
                     alt="Peta Indonesia" 
                     style="position: absolute; width: 100%; height: 100%; object-fit: cover; opacity: 0.4;">
                
                <div style="position: absolute; top: 65%; left: 28%; width: 25px; height: 25px; background: rgba(180, 82, 83, 0.8); border-radius: 50%; box-shadow: 0 0 0 5px rgba(180, 82, 83, 0.3);" title="Jakarta (High)"></div>
                <div style="position: absolute; top: 68%; left: 32%; width: 15px; height: 15px; background: rgba(252, 181, 59, 0.8); border-radius: 50%;" title="Bandung (Medium)"></div>
                <div style="position: absolute; top: 67%; left: 45%; width: 18px; height: 18px; background: rgba(132, 153, 79, 0.8); border-radius: 50%;" title="Surabaya (Medium)"></div>
                
                <div style="position: absolute; bottom: 20px; right: 20px; background: white; padding: 10px; border-radius: 8px; font-size: 0.8rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <div class="d-flex align-items-center mb-1"><span style="width:10px; height:10px; background:#B45253; border-radius:50%; display:inline-block; margin-right:5px;"></span> Tinggi</div>
                    <div class="d-flex align-items-center mb-1"><span style="width:10px; height:10px; background:#FCB53B; border-radius:50%; display:inline-block; margin-right:5px;"></span> Sedang</div>
                    <div class="d-flex align-items-center"><span style="width:10px; height:10px; background:#84994F; border-radius:50%; display:inline-block; margin-right:5px;"></span> Rendah</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white fw-bold py-3">Top Kota Pembelian</div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Kota</th>
                            <th>Vol. Order</th>
                            <th class="text-end pe-4">Pertumbuhan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-bold">Jakarta Selatan</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">1,240</span>
                                    <div class="volume-bar w-100 mt-1"><div class="volume-fill" style="width: 90%;"></div></div>
                                </div>
                            </td>
                            <td class="text-end pe-4 text-success"><i class="bi bi-arrow-up"></i> 12%</td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-bold">Bandung</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">850</span>
                                    <div class="volume-bar w-100 mt-1"><div class="volume-fill bg-warning" style="width: 65%;"></div></div>
                                </div>
                            </td>
                            <td class="text-end pe-4 text-success"><i class="bi bi-arrow-up"></i> 8%</td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-bold">Surabaya</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">420</span>
                                    <div class="volume-bar w-100 mt-1"><div class="volume-fill bg-success" style="width: 40%;"></div></div>
                                </div>
                            </td>
                            <td class="text-end pe-4 text-danger"><i class="bi bi-arrow-down"></i> 2%</td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-bold">Medan</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">310</span>
                                    <div class="volume-bar w-100 mt-1"><div class="volume-fill bg-success" style="width: 30%;"></div></div>
                                </div>
                            </td>
                            <td class="text-end pe-4 text-success"><i class="bi bi-arrow-up"></i> 5%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-center py-3">
                <button class="btn btn-sm btn-outline-secondary">Lihat Semua Lokasi</button>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h4 class="section-title mb-0">Top Pelanggan & Segmen</h4>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <button class="btn btn-sm btn-primary">
                    <i class="bi bi-download me-1"></i> Export Laporan
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Pelanggan</th>
                                <th>Segmen</th>
                                <th class="text-center">Total Order (6 Bln)</th>
                                <th>Total Belanja</th>
                                <th>Terakhir Beli</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light text-dark d-flex justify-content-center align-items-center me-3 fw-bold border" style="width: 40px; height: 40px;">AS</div>
                                        <div>
                                            <div class="fw-bold">Ahmad Subagja</div>
                                            <small class="text-muted">ahmad@dummy.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-loyal rounded-pill px-3">Pelanggan Loyal</span></td>
                                <td class="fw-bold text-center fs-5">24</td>
                                <td>Rp 5.400.000</td>
                                <td>2 hari lalu</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" title="Kirim Hadiah / Diskon Khusus">
                                        <i class="bi bi-gift-fill me-1"></i> Kirim Reward
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light text-dark d-flex justify-content-center align-items-center me-3 fw-bold border" style="width: 40px; height: 40px;">SL</div>
                                        <div>
                                            <div class="fw-bold">Siti Lestari</div>
                                            <small class="text-muted">siti@dummy.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-aktif rounded-pill px-3">Pelanggan Aktif</span></td>
                                <td class="fw-bold text-center fs-5">10</td>
                                <td>Rp 1.850.000</td>
                                <td>1 minggu lalu</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" title="Kirim Hadiah / Diskon Khusus">
                                        <i class="bi bi-gift-fill me-1"></i> Kirim Reward
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light text-dark d-flex justify-content-center align-items-center me-3 fw-bold border" style="width: 40px; height: 40px;">BH</div>
                                        <div>
                                            <div class="fw-bold">Budi Hartono</div>
                                            <small class="text-muted">budi@dummy.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-baru rounded-pill px-3">Pelanggan Baru</span></td>
                                <td class="fw-bold text-center fs-5">1</td>
                                <td>Rp 150.000</td>
                                <td>3 minggu lalu</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" title="Kirim Hadiah / Diskon Khusus">
                                        <i class="bi bi-gift-fill me-1"></i> Kirim Reward
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-center py-3">
                <a href="#" class="text-decoration-none fw-bold" style="color: #84994F;">Lihat Seluruh Data Pelanggan &rarr;</a>
            </div>
        </div>
    </div>
</div>

@endsection