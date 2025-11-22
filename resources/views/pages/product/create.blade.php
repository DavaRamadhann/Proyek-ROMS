@extends('layout.main')

@section('title', 'Tambah Produk')

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #FCB53B 0%, #e09d22 100%);
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
        border-bottom: 3px solid #FCB53B;
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
        border-color: #FCB53B;
        box-shadow: 0 0 0 0.2rem rgba(252, 181, 59, 0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #FCB53B 0%, #e09d22 100%);
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        color: #fff;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(252, 181, 59, 0.3);
    }
    
    .btn-secondary {
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }
        
        .form-card .card-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="text-white">
            <h2 class="mb-2">
                <i class="bi bi-box-seam me-2"></i>Tambah Produk
            </h2>
            <p class="mb-0 opacity-75">Tambahkan produk baru ke dalam inventori</p>
        </div>
    </div>
    <div class="breadcrumb-custom mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('product.index') }}">Produk</a></li>
                <li class="breadcrumb-item active">Tambah Produk</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card form-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Data Produk</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('product.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Kopi Arabika 500g">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SKU (Stock Keeping Unit) <span class="text-danger">*</span></label>
                        <input type="text" name="sku" class="form-control" required placeholder="Contoh: KOP-ARB-500">
                        <small class="text-muted">Kode unik untuk setiap produk.</small>
                    </div>

                    {{-- Jika database Anda punya kolom price, tambahkan input di sini --}}
                    {{-- 
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="price" class="form-control">
                    </div> 
                    --}}

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('product.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection