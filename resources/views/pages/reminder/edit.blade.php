@extends('layout.main')

@section('title', 'Edit Aturan')

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
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
        border-bottom: 3px solid #ffc107;
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
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        color: #000;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
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
                <i class="bi bi-pencil-square me-2"></i>Edit Aturan Pengingat
            </h2>
            <p class="mb-0 opacity-75">Perbarui aturan: {{ $rule->name }}</p>
        </div>
    </div>
    <div class="breadcrumb-custom mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reminder.index') }}">Reminder</a></li>
                <li class="breadcrumb-item active">Edit Aturan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card form-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Edit Aturan Pengingat</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reminder.update', $rule->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Aturan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $rule->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kirim Berapa Hari Setelah Dikirim? <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="days_after_shipped" class="form-control" value="{{ $rule->days_after_shipped }}" min="1" required>
                            <span class="input-group-text">Hari setelah status 'Shipped'</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Template Pesan WhatsApp <span class="text-danger">*</span></label>
                        <textarea name="message_template" class="form-control" rows="5" required>{{ $rule->message_template }}</textarea>
                        <div class="form-text text-muted">
                            Gunakan <code>{name}</code> dan <code>{order_number}</code> sebagai variabel otomatis.
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" {{ $rule->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Aktifkan Aturan Ini</label>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('reminder.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update Aturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection