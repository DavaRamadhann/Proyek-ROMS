@extends('layout.main')

@section('title', 'Buat Pesanan Baru')

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #B45253 0%, #8d4142 100%);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .breadcrumb-custom {
        background: rgba(255,255,255,0.1);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        backdrop-filter: blur(10px);
    }
    
    .breadcrumb-custom .breadcrumb {
        margin: 0;
    }
    
    .breadcrumb-custom .breadcrumb-item,
    .breadcrumb-custom .breadcrumb-item a {
        color: rgba(255,255,255,0.9);
        font-size: 0.9rem;
    }
    
    .breadcrumb-custom .breadcrumb-item.active {
        color: #fff;
        font-weight: 500;
    }
    
    .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255,255,255,0.6);
    }
    
    .form-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .form-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 3px solid #B45253;
        padding: 1.25rem 1.5rem;
    }
    
    .form-card .card-body {
        padding: 2rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 0.625rem 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #B45253;
        box-shadow: 0 0 0 0.2rem rgba(180, 82, 83, 0.15);
    }
    
    .table {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #B45253 0%, #8d4142 100%);
        color: white;
        font-weight: 600;
        border: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #B45253 0%, #8d4142 100%);
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(180, 82, 83, 0.3);
    }
    
    .btn-secondary {
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
    
    .total-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.5rem;
        border: 2px solid #B45253;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }
        
        .form-card .card-body {
            padding: 1.5rem;
        }
        
        .table {
            font-size: 0.85rem;
        }
    }
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="text-white">
            <h2 class="mb-2">
                <i class="bi bi-cart-plus me-2"></i>Buat Pesanan Baru
            </h2>
            <p class="mb-0 opacity-75">Tambahkan pesanan baru dari pelanggan</p>
        </div>
    </div>
    <div class="breadcrumb-custom mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('order.index') }}">Pesanan</a></li>
                <li class="breadcrumb-item active">Buat Pesanan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="card form-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Pesanan Baru</h5>
            </div>
            <div class="card-body">
                {{-- Tampilkan Error Validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('order.store') }}" method="POST">
                    @csrf
                    
                    {{-- Pilih Pelanggan --}}
                    <div class="mb-4">
                        <label class="form-label">Pilih Pelanggan <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">-- Cari Pelanggan --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Daftar Barang --}}
                    <h5 class="fw-bold mb-3"><i class="bi bi-basket me-2"></i>Daftar Barang</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%">Produk</th>
                                    <th style="width: 15%">Jumlah</th>
                                    <th style="width: 25%">Harga Satuan (Rp)</th>
                                    <th style="width: 15%">Subtotal</th>
                                    <th style="width: 5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                {{-- Baris Pertama (Default) --}}
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][product_id]" class="form-select product-select" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($products as $product)
                                                {{-- Kita simpan harga di data-attribute agar bisa diambil JS --}}
                                                <option value="{{ $product->id }}">
                                                    {{ $product->name }} ({{ $product->sku }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control quantity-input" value="1" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][price]" class="form-control price-input" value="0" min="0" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control subtotal-display" value="0" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row-btn" disabled>X</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                        <button type="button" class="btn btn-success" id="add-row-btn">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Barang Lain
                        </button>
                        <div class="total-section">
                            <h4 class="mb-0 text-center">Total: <span id="grand-total" class="text-danger">Rp 0</span></h4>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('order.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script JavaScript Sederhana --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowCount = 1;
        const tableBody = document.getElementById('items-body');
        const addBtn = document.getElementById('add-row-btn');
        const grandTotalEl = document.getElementById('grand-total');

        // Fungsi Hitung Total
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const subtotal = qty * price;
                
                row.querySelector('.subtotal-display').value = subtotal.toLocaleString('id-ID');
                total += subtotal;
            });
            grandTotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Event Listener untuk Input (agar total otomatis update)
        tableBody.addEventListener('input', function (e) {
            if (e.target.classList.contains('quantity-input') || e.target.classList.contains('price-input')) {
                calculateTotal();
            }
        });

        // Tambah Baris Baru
        addBtn.addEventListener('click', function () {
            // Ambil baris pertama sebagai template
            const firstRow = tableBody.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);
            
            // Reset nilai input di baris baru
            newRow.querySelectorAll('input').forEach(input => input.value = (input.name.includes('quantity') ? 1 : 0));
            newRow.querySelector('.remove-row-btn').disabled = false; // Aktifkan tombol hapus

            // Update nama input array index: items[0] -> items[1]
            newRow.querySelectorAll('select, input').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);
                }
            });

            tableBody.appendChild(newRow);
            rowCount++;
            calculateTotal();
        });

        // Hapus Baris
        tableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row-btn')) {
                e.target.closest('tr').remove();
                calculateTotal();
            }
        });
    });
</script>
@endsection