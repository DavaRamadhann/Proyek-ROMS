@extends('layout.main')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Broadcast</h1>


    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Informasi Broadcast</div>
                <div class="card-body">
                    <p><strong>Judul:</strong> {{ $broadcast->name }}</p>
                    <p><strong>Status:</strong> 
                        @if($broadcast->status == 'draft') <span class="badge bg-secondary">Draft</span>
                        @elseif($broadcast->status == 'scheduled') <span class="badge bg-warning text-dark">Terjadwal</span>
                        @elseif($broadcast->status == 'processing') <span class="badge bg-primary">Memproses</span>
                        @elseif($broadcast->status == 'completed') <span class="badge bg-success">Selesai</span>
                        @endif
                    </p>
                    <p><strong>Target:</strong> {{ ucfirst($broadcast->target_segment) }}</p>
                    <p><strong>Jadwal:</strong> {{ $broadcast->scheduled_at ? $broadcast->scheduled_at->format('d M Y H:i') : 'Langsung' }}</p>
                    <p><strong>Dibuat Oleh:</strong> {{ $broadcast->creator->name ?? '-' }}</p>
                    <hr>
                    <p><strong>Pesan:</strong></p>
                    <div class="p-2 bg-light border rounded mb-2">
                        {!! nl2br(e($broadcast->message_content)) !!}
                    </div>
                    @if($broadcast->attachment_url)
                        <p><strong>Lampiran:</strong></p>
                        @if($broadcast->attachment_type == 'image')
                            <img src="{{ $broadcast->attachment_url }}" class="img-fluid rounded" style="max-height: 200px;">
                        @else
                            <a href="{{ $broadcast->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-paperclip"></i> Lihat Lampiran</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i> Log Pengiriman
                    <span class="float-end badge bg-secondary">Total: {{ $broadcast->total_recipients }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3 text-center">
                        <div class="col-6">
                            <div class="p-3 bg-success text-white rounded">
                                <h3>{{ $broadcast->success_count }}</h3>
                                <small>Berhasil</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-danger text-white rounded">
                                <h3>{{ $broadcast->fail_count }}</h3>
                                <small>Gagal</small>
                            </div>
                        </div>
                    </div>

                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>No. HP</th>
                                <th>Status</th>
                                <th>Waktu / Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($broadcast->logs as $log)
                            <tr>
                                <td>{{ $log->customer->name ?? 'Unknown' }}</td>
                                <td>{{ $log->customer->phone ?? '-' }}</td>
                                <td>
                                    @if($log->status == 'sent') <span class="badge bg-success">Terkirim</span>
                                    @else <span class="badge bg-danger">Gagal</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->status == 'sent')
                                        {{ $log->sent_at ? $log->sent_at->format('H:i:s') : '-' }}
                                    @else
                                        <span class="text-danger small">{{ Str::limit($log->error_message, 50) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada log pengiriman.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
