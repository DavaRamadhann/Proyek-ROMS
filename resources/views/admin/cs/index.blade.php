@extends('layout.main')

@section('title', 'Kelola CS - ROMS')

@section('search-placeholder', 'Cari CS...')

@push('styles')
<style>
    .cs-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .cs-table th {
        background-color: #84994F;
        color: white;
        font-weight: 600;
        padding: 15px;
    }
    .cs-table td {
        padding: 15px;
        vertical-align: middle;
    }
    .badge-cs {
        background-color: #0d6efd;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('main-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        <i class="bi bi-people-fill me-2"></i>Kelola Customer Service
    </h2>
    <a href="{{ route('admin.cs.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Tambah CS Baru
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card cs-table border-0">
    <div class="card-body p-0">
        @if($csUsers->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 30%">Nama</th>
                        <th style="width: 25%">Email</th>
                        <th style="width: 15%">Status</th>
                        <th style="width: 15%">Terdaftar</th>
                        <th style="width: 10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($csUsers as $index => $cs)
                    <tr>
                        <td>{{ $csUsers->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($cs->name, 0, 1)) }}
                                </div>
                                <strong>{{ $cs->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $cs->email }}</td>
                        <td>
                            @if($cs->is_online)
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Online</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3 py-2"><i class="bi bi-moon-fill me-1" style="font-size: 0.5rem;"></i> Offline</span>
                            @endif
                        </td>
                        <td>{{ $cs->created_at->format('d M Y') }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.cs.edit', $cs->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.cs.destroy', $cs->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus CS ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-3">
            {{ $csUsers->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-people" style="font-size: 4rem; color: #ddd;"></i>
            <p class="text-muted mt-3">Belum ada CS yang terdaftar.</p>
            <a href="{{ route('admin.cs.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>Tambah CS Pertama
            </a>
        </div>
        @endif
    </div>
</div>

@endsection
