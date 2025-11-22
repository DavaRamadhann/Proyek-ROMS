@extends('layout.main')

@section('title', 'Edit Pelanggan')

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #84994F 0%, #6d7d40 100%);
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
        border-bottom: 3px solid #84994F;
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
        border-color: #84994F;
        box-shadow: 0 0 0 0.2rem rgba(132, 153, 79, 0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #84994F 0%, #6d7d40 100%);
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(132, 153, 79, 0.3);
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
                <i class="bi bi-pencil-square me-2"></i>Edit Pelanggan
            </h2>
            <p class="mb-0 opacity-75">Perbarui data pelanggan: {{ $customer->name }}</p>
        </div>
    </div>
    <div class="breadcrumb-custom mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Pelanggan</a></li>
                <li class="breadcrumb-item active">Edit Pelanggan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card form-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Edit Data Pelanggan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('customer.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. WhatsApp / HP <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $customer->email }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kota/Kabupaten</label>
                            <input type="text" name="city" class="form-control" value="{{ $customer->city }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Segmen</label>
                            <select name="segment_tag" class="form-select">
                                <option value="new" {{ $customer->segment_tag == 'new' ? 'selected' : '' }}>New</option>
                                <option value="loyal" {{ $customer->segment_tag == 'loyal' ? 'selected' : '' }}>Loyal</option>
                                <option value="vip" {{ $customer->segment_tag == 'vip' ? 'selected' : '' }}>VIP</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                         <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ $customer->status == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                         </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="3">{{ $customer->address }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('customer.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection