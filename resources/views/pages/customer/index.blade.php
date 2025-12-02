@extends('layout.main')

@section('title', 'Daftar Pelanggan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Daftar Pelanggan</h2>
        <p class="text-muted">Kelola data pelanggan dan informasi kontak.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white py-3">
        <form action="{{ route('customers.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau nomor HP..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Cari
            </button>
            @if(request('search'))
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Nama Pelanggan</th>
                        <th class="py-3">Nomor WhatsApp</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Tag</th>
                        <th class="py-3">Status Nama</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $customer->name }}</div>
                            <small class="text-muted">ID: {{ $customer->id }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-whatsapp text-success me-2"></i>
                                <span class="font-monospace">{{ $customer->clean_phone }}</span>
                            </div>
                        </td>
                        <td>
                            {{ $customer->email ?? '-' }}
                        </td>
                        <td>
                            @if($customer->segment_tag)
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill">
                                    {{ $customer->segment_tag }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($customer->is_manual_name)
                                <span class="badge bg-warning bg-opacity-10 text-warning" title="Nama diedit manual (dikunci)">
                                    <i class="bi bi-lock-fill"></i> Manual
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary" title="Nama dari WhatsApp (auto-update)">
                                    <i class="bi bi-arrow-repeat"></i> Auto
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            {{-- Tombol hapus disembunyikan jika ada relasi, tapi controller tetap handle validasi --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" alt="Empty" style="width: 60px; opacity: 0.5;">
                            <p class="text-muted mt-3">Belum ada data pelanggan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection