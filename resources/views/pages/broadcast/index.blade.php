@extends('layout.main')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Broadcast WhatsApp</h1>


    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-bullhorn me-1"></i> Riwayat Broadcast</div>
            <a href="{{ route('broadcast.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Buat Broadcast Baru
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Judul Broadcast</th>
                        <th>Target</th>
                        <th>Status</th>
                        <th>Jadwal</th>
                        <th>Penerima</th>
                        <th>Sukses/Gagal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($broadcasts as $broadcast)
                    <tr>
                        <td>{{ $broadcast->name }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ ucfirst($broadcast->target_segment) }}</span>
                        </td>
                        <td>
                            @if($broadcast->status == 'draft') <span class="badge bg-secondary">Draft</span>
                            @elseif($broadcast->status == 'scheduled') <span class="badge bg-warning text-dark">Terjadwal</span>
                            @elseif($broadcast->status == 'processing') <span class="badge bg-primary">Memproses</span>
                            @elseif($broadcast->status == 'completed') <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                        <td>
                            {{ $broadcast->scheduled_at ? $broadcast->scheduled_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td>{{ $broadcast->total_recipients }}</td>
                        <td>
                            <span class="text-success">{{ $broadcast->success_count }}</span> / 
                            <span class="text-danger">{{ $broadcast->fail_count }}</span>
                        </td>
                        <td>
                            <a href="{{ route('broadcast.show', $broadcast->id) }}" class="btn btn-sm btn-info text-white">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada kampanye broadcast.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{ $broadcasts->links() }}
        </div>
    </div>
</div>
@endsection
