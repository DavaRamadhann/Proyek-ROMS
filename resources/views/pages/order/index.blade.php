@extends('layout.main')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Daftar Pesanan</h2>
        <p class="text-muted">Kelola semua pesanan masuk dan status pengiriman.</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary" style="background-color: #B45253; border-color: #B45253;">
        <i class="bi bi-plus-lg me-1"></i> Buat Pesanan Baru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">No. Order</th>
                        <th class="py-3">Pelanggan</th>
                        <th class="py-3">Total</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Tanggal</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">
                            <a href="{{ route('orders.show', $order->id) }}" class="text-decoration-none">{{ $order->order_number }}</a>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $order->customer->name }}</div>
                            <small class="text-muted">{{ $order->customer->phone }}</small>
                        </td>
                        <td class="fw-bold" style="color: #84994F;">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($order->status == 'completed')
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Selesai</span>
                            @elseif($order->status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Pending</span>
                            @elseif($order->status == 'shipped')
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">Dikirim</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i> {{ $order->created_at->format('d M Y') }}
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" alt="Empty" style="width: 80px; opacity: 0.5;">
                            <p class="text-muted mt-3">Belum ada pesanan yang tercatat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection