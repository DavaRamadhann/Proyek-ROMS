@extends('layout.cs_main')

@section('title', 'Riwayat Pesanan - ROMS')

@push('styles')
<style>
    .badge-status-pending { background-color: #FCB53B; color: #333; }
    .badge-status-success { background-color: #84994F; color: white; }
    .badge-status-danger { background-color: #B45253; color: white; }
    
    .table-card {
        border: none; border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }
</style>
@endpush

@section('main-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">Riwayat Pesanan</h2>
    <div>
        <button class="btn btn-outline-secondary me-2"><i class="bi bi-filter"></i> Filter</button>
        <button class="btn btn-maroon" style="background:#B45253;color:white;"><i class="bi bi-download"></i> Export</button>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3">ID Order</th>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td class="px-4 fw-bold text-maroon">#{{ $item->id }}</td>
                    <td class="px-4">{{ $item->customer->name ?? '-' }}</td>
                    <td class="px-4">{{ $item->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 fw-bold">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                    <td class="px-4">
                        {{-- Logika badge sederhana --}}
                        @if($item->status == 'paid')
                            <span class="badge badge-status-success">Dibayar</span>
                        @elseif($item->status == 'pending')
                             <span class="badge badge-status-pending">Menunggu</span>
                        @else
                             <span class="badge badge-status-danger">{{ $item->status }}</span>
                        @endif
                    </td>
                    <td class="px-4 text-end">
                        <button class="btn btn-sm btn-outline-secondary">Detail</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection