@extends('layout.main')

@section('title', 'Manajemen Template Pesan')

@section('main-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Template Pesan</h2>
            <p class="text-muted">Kelola format pesan untuk Broadcast dan Reminder.</p>
        </div>
        <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Buat Template
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama Template</th>
                            <th>Tipe</th>
                            <th>Preview Konten</th>
                            <th>Variabel</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $template->name }}</td>
                            <td>
                                <span class="badge bg-{{ $template->type == 'broadcast' ? 'info' : ($template->type == 'reminder' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($template->type) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($template->content, 50) }}</small>
                            </td>
                            <td>
                                @if($template->variables)
                                    @foreach($template->variables as $var)
                                        <span class="badge bg-light text-dark border">{{ $var }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                                Belum ada template pesan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $templates->links() }}
        </div>
    </div>
</div>
@endsection
