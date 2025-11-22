@extends('layout.main')

@section('title', 'Manajemen Produk - ROMS')

@section('search-placeholder', 'Cari produk berdasarkan nama atau SKU...')

@section('topbar-actions')
<a href="{{ route('product.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i><span class="d-none d-lg-inline">Tambah Produk</span>
</a>
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #FCB53B 0%, #e0a030 100%);
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
        border-left: 4px solid #FCB53B;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .data-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: none;
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
        <h3 class="mb-1 fw-bold"><i class="bi bi-box-seam-fill me-2"></i>Data Produk</h3>
        <p class="mb-0 opacity-75">Kelola katalog produk dan inventori</p>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <small class="text-muted">Total Produk</small>
            <h4 class="mb-0 fw-bold" style="color: #FCB53B;">{{ $products->total() }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="border-left-color: #84994F;">
            <small class="text-muted">SKU Unik</small>
            <h4 class="mb-0 fw-bold" style="color: #84994F;">{{ $products->total() }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="border-left-color: #B45253;">
            <small class="text-muted">Halaman</small>
            <h4 class="mb-0 fw-bold" style="color: #B45253;">{{ $products->currentPage() }} / {{ $products->lastPage() }}</h4>
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
                        <th>Nama Produk</th>
                        <th>SKU (Kode)</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="fw-semibold">{{ $product->name }}</td>
                        <td>
                            <span class="badge" style="background-color: #FCB53B; color: #333;">{{ $product->sku }}</span>
                        </td>
                        <td>{{ $product->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('product.edit', $product->id) }}" class="action-btn btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus produk ini?')">
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
                        <td colspan="4" class="text-center text-muted">Belum ada data produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginasi --}}
        <div class="mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection