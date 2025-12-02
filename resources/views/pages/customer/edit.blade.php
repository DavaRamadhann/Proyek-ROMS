@extends('layout.main')

@section('title', 'Edit Pelanggan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Edit Pelanggan</h2>
        <p class="text-muted">Perbarui informasi pelanggan.</p>
    </div>
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px; max-width: 800px;">
    <div class="card-body p-4">
        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="form-label fw-semibold">Nomor WhatsApp</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-success"></i></span>
                    <input type="text" class="form-control bg-light" value="{{ $customer->clean_phone }}" readonly disabled>
                </div>
                <div class="form-text">Nomor WhatsApp tidak dapat diubah karena menjadi identitas utama.</div>
            </div>

            <div class="mb-4">
                <label for="name" class="form-label fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text text-info">
                    <i class="bi bi-info-circle"></i> Jika Anda mengubah nama ini, sistem tidak akan lagi menimpanya dengan nama profil WhatsApp pelanggan.
                </div>
            </div>

            <div class="mb-4">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email) }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>



            <div class="d-flex justify-content-end gap-2 mt-5">
                <a href="{{ route('customers.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection