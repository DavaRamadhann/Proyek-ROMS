@extends('layout.main')

@section('title', 'Integrasi API - ROMS')

@section('main-content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">Integrasi API</h2>
            <p class="text-muted">Hubungkan toko online atau sistem eksternal Anda dengan ROMS.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-code-slash me-2"></i>Dokumentasi API Order</h5>
                </div>
                <div class="card-body">
                    <p>Gunakan endpoint di bawah ini untuk mengirim data pesanan baru ke dalam sistem ROMS secara otomatis.</p>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Endpoint URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">POST</span>
                            <input type="text" class="form-control font-monospace" value="{{ $apiUrl }}" readonly>
                            <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ $apiUrl }}')">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4">Header Request</h6>
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                                <th>Wajib?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>Content-Type</code></td>
                                <td><code>application/json</code></td>
                                <td>Ya</td>
                            </tr>
                            <tr>
                                <td><code>Accept</code></td>
                                <td><code>application/json</code></td>
                                <td>Ya</td>
                            </tr>
                            <tr>
                                <td><code>X-API-KEY</code></td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control font-monospace" value="{{ env('API_KEY', 'someah-secret-key') }}" readonly>
                                        <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ env('API_KEY', 'someah-secret-key') }}')">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </td>
                                <td><span class="badge bg-danger">Wajib</span></td>
                            </tr>
                        </tbody>
                    </table>

                    <h6 class="fw-bold mt-4">Contoh Body (JSON)</h6>
                    <div class="bg-dark text-white p-3 rounded position-relative">
<pre class="mb-0"><code>{
  "customer": {
    "phone": "081234567890",
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "address": "Jl. Merdeka No. 45, Jakarta"
  },
  "items": [
    {
      "product_name": "Kopi Arabika 200g",
      "quantity": 2,
      "price": 75000
    }
  ],
  "notes": "Tolong dikirim segera",
  "external_id": "SHOPIFY-1001"
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-lightbulb-fill me-2"></i>Tips Integrasi</h5>
                    <ul class="mt-3 mb-0 ps-3">
                        <li class="mb-2">Pastikan nomor HP diawali dengan <code>08</code> atau <code>62</code>.</li>
                        <li class="mb-2">Sistem akan otomatis membuat data pelanggan baru jika nomor HP belum terdaftar.</li>
                        <li class="mb-2">Gunakan <code>external_id</code> untuk mencegah duplikasi pesanan dari sistem Anda.</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Status Koneksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">API Aktif</h6>
                            <small class="text-muted">Siap menerima request</small>
                        </div>
                    </div>
                    <hr>
                    <small class="text-muted d-block mb-2">Butuh bantuan teknis?</small>
                    <a href="#" class="btn btn-outline-primary btn-sm w-100">Hubungi Developer</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
