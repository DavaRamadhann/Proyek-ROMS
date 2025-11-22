@extends('layout.cs_main')

@section('title', 'Detail Pesanan #12345 - ROMS')

@push('styles')
<style>
    /* Palet Warna */
    :root {
        --color-maroon: #B45253;
        --color-gold: #FCB53B;
        --color-green: #84994F;
    }
    
    .dashboard-header {
        font-size: 1.5rem; font-weight: 700; color: #333;
    }
    .card {
        border-radius: 12px; border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px;
    }
    .card-header {
        background-color: white; border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px; font-weight: 700;
    }

    /* Timeline Status */
    .status-timeline {
        display: flex; justify-content: space-between; margin-bottom: 20px; position: relative;
    }
    .status-timeline::before {
        content: ''; position: absolute; top: 15px; left: 0; right: 0;
        height: 2px; background-color: #e9ecef; z-index: 0;
    }
    .timeline-item {
        position: relative; z-index: 1; text-align: center; width: 25%;
    }
    .timeline-dot {
        width: 32px; height: 32px; border-radius: 50%; background-color: #e9ecef;
        margin: 0 auto 10px; display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: bold;
    }
    .timeline-item.active .timeline-dot {
        background-color: var(--color-green);
    }
    .timeline-item.active::before { 
        content: ''; position: absolute; top: 15px; left: -50%; width: 100%;
        height: 2px; background-color: var(--color-green); z-index: -1;
    }
    .timeline-item:first-child.active::before { display: none; }
    
    .product-img {
        width: 60px; height: 60px; object-fit: cover; border-radius: 8px;
        background-color: #eee;
    }
</style>
@endpush

@section('main-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="/cs-pesanan" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
        <h2 class="dashboard-header mt-1">Order #12345</h2>
    </div>
    
    <div>
        <span class="badge bg-success fs-6 px-3 py-2">Selesai</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        
        <div class="card">
            <div class="card-body p-4">
                <div class="status-timeline">
                    <div class="timeline-item active">
                        <div class="timeline-dot"><i class="bi bi-check"></i></div>
                        <small class="fw-bold">Pesanan Dibuat</small>
                        <div class="text-muted small">10 Nov, 09:00</div>
                    </div>
                    <div class="timeline-item active">
                        <div class="timeline-dot"><i class="bi bi-check"></i></div>
                        <small class="fw-bold">Diproses</small>
                        <div class="text-muted small">10 Nov, 14:00</div>
                    </div>
                    <div class="timeline-item active">
                        <div class="timeline-dot"><i class="bi bi-check"></i></div>
                        <small class="fw-bold">Dikirim</small>
                        <div class="text-muted small">11 Nov, 08:00</div>
                    </div>
                    <div class="timeline-item active">
                        <div class="timeline-dot"><i class="bi bi-check"></i></div>
                        <small class="fw-bold">Selesai</small>
                        <div class="text-muted small">12 Nov, 15:30</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Rincian Produk</div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th class="text-end pe-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="product-img me-3 d-flex align-items-center justify-content-center text-muted">Img</div>
                                    <div>
                                        <div class="fw-bold">Kopi Arabika Gayo</div>
                                        <small class="text-muted">Varian: 250gr, Biji</small>
                                    </div>
                                </div>
                            </td>
                            <td>Rp 75.000</td>
                            <td>2</td>
                            <td class="text-end pe-4 fw-bold">Rp 150.000</td>
                        </tr>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="product-img me-3 d-flex align-items-center justify-content-center text-muted">Img</div>
                                    <div>
                                        <div class="fw-bold">Filter Kopi V60</div>
                                        <small class="text-muted">Isi: 100pcs</small>
                                    </div>
                                </div>
                            </td>
                            <td>Rp 45.000</td>
                            <td>1</td>
                            <td class="text-end pe-4 fw-bold">Rp 45.000</td>
                        </tr>
                    </tbody>
                    <tfoot style="border-top: 2px solid #eee;">
                        <tr>
                            <td colspan="3" class="text-end pt-3">Subtotal</td>
                            <td class="text-end pe-4 pt-3">Rp 195.000</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end text-muted">Ongkos Kirim (JNE REG)</td>
                            <td class="text-end pe-4 text-muted">Rp 10.000</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold fs-5 pb-3">Total Bayar</td>
                            <td class="text-end pe-4 fw-bold fs-5 pb-3" style="color: var(--color-maroon);">Rp 205.000</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        
        {{-- CARD AKSI PESANAN SUDAH DIHAPUS --}}

        <div class="card">
            <div class="card-header">Info Pelanggan</div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm me-3 rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:40px; height:40px;">AS</div>
                    <div>
                        <h6 class="mb-0 fw-bold">Ahmad Subagja</h6>
                        <span class="badge bg-success" style="font-size: 0.7rem;">Pelanggan Loyal</span>
                    </div>
                </div>
                <div class="small text-muted">
                    <div class="mb-2"><i class="bi bi-envelope me-2"></i> ahmad@dummy.com</div>
                    <div class="mb-2"><i class="bi bi-telephone me-2"></i> 0812-3456-7890</div>
                    <div class="mb-0"><i class="bi bi-geo-alt me-2"></i> Jl. Kopi No. 1, Jakarta</div>
                </div>
                <hr>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Total Pesanan</span>
                    <span class="fw-bold">5x</span>
                </div>
                <div class="d-flex justify-content-between small mt-1">
                    <span class="text-muted">Total Belanja</span>
                    <span class="fw-bold">Rp 1.250.000</span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Komplain tetap ada di kode (hidden), siapa tahu nanti dipanggil dari tempat lain --}}
<div class="modal fade" id="modalKomplain" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger">Catat Komplain Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Komplain</label>
                        <select class="form-select">
                            <option>Barang Rusak / Cacat</option>
                            <option>Barang Tidak Sesuai (Salah Kirim)</option>
                            <option>Pengiriman Terlambat</option>
                            <option>Kemasan Rusak</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Masalah</label>
                        <textarea class="form-control" rows="4" placeholder="Jelaskan detail komplain pelanggan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger">Simpan Komplain</button>
            </div>
        </div>
    </div>
</div>

@endsection