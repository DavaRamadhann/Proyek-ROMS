@extends('layout.cs_main')

@section('title', 'Data Pelanggan - ROMS')

@push('styles')
<style>
    .badge-maroon { background-color: #B45253; color: white; }
    .badge-green { background-color: #84994F; color: white; }
    .badge-gold { background-color: #FCB53B; color: #333; }
    
    .table-card {
        border: none; border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .avatar-sm {
        width: 35px; height: 35px; border-radius: 50%;
        background: #eee; color: #555;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 14px;
    }
</style>
@endpush

@section('main-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">Data Pelanggan</h2>
    <div class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control border-end-0" placeholder="Cari pelanggan...">
        <span class="input-group-text bg-white border-start-0"><i class="bi bi-search"></i></span>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3">Kontak</th>
                    <th class="px-4 py-3">Total Belanja</th>
                    <th class="px-4 py-3">Segmen</th>
                    <th class="px-4 py-3 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td class="px-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3 bg-light">{{ substr($item->name, 0, 1) }}</div>
                            <div>
                                <div class="fw-bold text-dark">{{ $item->name }}</div>
                                <small class="text-muted">Bergabung {{ $item->created_at->format('d M Y') }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="px-4">
                        <div class="small">{{ $item->phone }}</div>
                        <div class="small text-muted">{{ $item->email }}</div>
                    </td>
                    <td class="px-4 fw-bold text-dark">Rp {{ number_format($item->total_spent ?? 0, 0, ',', '.') }}</td>
                    <td class="px-4">
                        <span class="badge badge-green">Aktif</span>
                    </td>
                    <td class="px-4 text-end">
                        <button class="btn btn-sm btn-outline-secondary">Detail</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">Tidak ada data pelanggan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection