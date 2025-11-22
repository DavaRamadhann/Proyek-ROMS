@extends('layout.main')

@section('title', 'Manajemen Pelanggan - ROMS')

@section('search-placeholder', 'Cari pelanggan berdasarkan nama, telepon, atau email...')

@section('topbar-actions')
<a href="{{ route('customer.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i><span class="d-none d-lg-inline">Tambah Pelanggan</span>
</a>
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #84994F 0%, #6b7d3f 100%);
        color: white;
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 15px 20px;
        border-left: 4px solid #84994F;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .data-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: none;
    }
    .badge-custom {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .action-btn {
        padding: 6px 12px;
        font-size: 0.875rem;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('main-content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h3 class="mb-1 fw-bold"><i class="bi bi-people-fill me-2"></i>Data Pelanggan</h3>
        <p class="mb-0 opacity-75">Kelola data master pelanggan dan segmentasi</p>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <small class="text-muted">Total Pelanggan</small>
            <h4 class="mb-0 fw-bold" style="color: #84994F;">{{ $customers->total() }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="border-left-color: #FCB53B;">
            <small class="text-muted">Aktif</small>
            <h4 class="mb-0 fw-bold" style="color: #FCB53B;">{{ $customers->where('status', 'active')->count() }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="border-left-color: #B45253;">
            <small class="text-muted">Halaman</small>
            <h4 class="mb-0 fw-bold" style="color: #B45253;">{{ $customers->currentPage() }} / {{ $customers->lastPage() }}</h4>
        </div>
    </div>
</div>

{{-- Alert Sukses --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="data-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>No. HP</th>
                        <th>Email</th>
                        <th>Kota</th>
                        <th>Segmen</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                        <td>{{ $customer->city ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $customer->segment_tag ?? 'General' }}</span>
                        </td>
                        <td>
                            @if($customer->status == 'active')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('customer.edit', $customer->id) }}" class="action-btn btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <form action="{{ route('customer.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada data pelanggan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginasi --}}
        <div class="mt-3">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection