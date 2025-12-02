@extends('layout.main')

@section('title', 'Edit CS - ROMS')

@section('search-placeholder', 'Cari...')

@push('styles')
<style>
    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        padding: 30px;
    }
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    .form-control:focus {
        border-color: #84994F;
        box-shadow: 0 0 0 0.2rem rgba(132, 153, 79, 0.25);
    }
</style>
@endpush

@section('main-content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">
                <i class="bi bi-pencil me-2"></i>Edit Customer Service
            </h2>
            <a href="{{ route('admin.cs.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="form-card">
            <form action="{{ route('admin.cs.update', $csUser->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $csUser->name) }}" 
                           placeholder="Masukkan nama lengkap CS" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $csUser->email) }}" 
                           placeholder="contoh@someah.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Ubah Password (Opsional):</strong> Kosongkan jika tidak ingin mengubah password.
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" 
                           placeholder="Minimal 8 karakter (kosongkan jika tidak diubah)">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                           id="password_confirmation" name="password_confirmation" 
                           placeholder="Ulangi password baru">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Update CS
                    </button>
                    <a href="{{ route('admin.cs.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
