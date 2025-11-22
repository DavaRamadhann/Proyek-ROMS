@extends('layout.main')

@section('title', 'Manajemen Pesanan - ROMS')

@section('search-placeholder', 'Cari pesanan berdasarkan nomor pesanan atau pelanggan...')

@section('topbar-actions')
<a href="{{ route('order.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i><span class="d-none d-lg-inline">Buat Pesanan</span>
</a>
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #B45253 0%, #9a4243 100%);
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
        border-left: 4px solid #B45253;
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
        <h3 class="mb-1 fw-bold"><i class="bi bi-cart-fill me-2"></i>Data Pesanan</h3>
        <p class="mb-0 opacity-75">Kelola pesanan dan tracking pengiriman</p>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stats-card">
            <small class="text-muted">Total Pesanan</small>
            <h4 class="mb-0 fw-bold" style="color: #B45253;">{{ $orders->total() }}</h4>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stats-card" style="border-left-color: #ffc107;">
            <small class="text-muted">Pending</small>
            <h4 class="mb-0 fw-bold" style="color: #ffc107;">{{ $orders->where('status', 'pending')->count() }}</h4>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stats-card" style="border-left-color: #0dcaf0;">
            <small class="text-muted">Dikirim</small>
            <h4 class="mb-0 fw-bold" style="color: #0dcaf0;">{{ $orders->where('status', 'shipped')->count() }}</h4>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stats-card" style="border-left-color: #198754;">
            <small class="text-muted">Selesai</small>
            <h4 class="mb-0 fw-bold" style="color: #198754;">{{ $orders->where('status', 'completed')->count() }}</h4>
        </div>
    </div>
</div>

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
                        <th>No. Order</th>
                        <th>Pelanggan</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('order.show', $order->id) }}" class="fw-bold text-decoration-none">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->customer->name }}</td>
                        <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>
                            @if($order->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($order->status == 'shipped')
                                <span class="badge bg-info text-dark">Dikirim</span>
                            @elseif($order->status == 'completed')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-secondary">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('order.show', $order->id) }}" class="action-btn btn btn-sm btn-info text-white" title="Lihat Detail">
                                <i class="bi bi-eye-fill"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada pesanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection