@extends('layout.main')

@section('title', 'Aturan Reminder - ROMS')

@section('search-placeholder', 'Cari aturan reminder...')

@section('topbar-actions')
<a href="{{ route('reminder.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i><span class="d-none d-lg-inline">Buat Aturan</span>
</a>
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
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
        border-left: 4px solid #ffc107;
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
        <h3 class="mb-1 fw-bold"><i class="bi bi-bell-fill me-2"></i>Aturan Reminder</h3>
        <p class="mb-0 opacity-75">Kelola reminder otomatis untuk repeat order</p>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6">
        <div class="stats-card">
            <small class="text-muted">Total Aturan</small>
            <h4 class="mb-0 fw-bold" style="color: #ffc107;">{{ $rules->count() }}</h4>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="stats-card" style="border-left-color: #198754;">
            <small class="text-muted">Aktif</small>
            <h4 class="mb-0 fw-bold" style="color: #198754;">{{ $rules->where('is_active', true)->count() }}</h4>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="stats-card" style="border-left-color: #6c757d;">
            <small class="text-muted">Non-Aktif</small>
            <h4 class="mb-0 fw-bold" style="color: #6c757d;">{{ $rules->where('is_active', false)->count() }}</h4>
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
                        <th>Nama Aturan</th>
                        <th>Jadwal Kirim</th>
                        <th>Isi Pesan Template</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                    <tr>
                        <td class="fw-bold">{{ $rule->name }}</td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $rule->days_after_shipped }} Hari
                            </span>
                            <small class="text-muted d-block">setelah status 'shipped'</small>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ Str::limit($rule->message_template, 50) }}
                            </small>
                        </td>
                        <td>
                            @if($rule->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('reminder.edit', $rule->id) }}" class="action-btn btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <form action="{{ route('reminder.destroy', $rule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus aturan ini?')">
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
                        <td colspan="5" class="text-center text-muted">Belum ada aturan pengingat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection