@extends('layout.main')

@section('title', 'Detail Reminder - ROMS')

@section('main-content')

<div class="row">
    <div class="col-lg-12">
        {{-- Header --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-eye-fill me-2"></i>Detail Reminder</h5>
                <a href="{{ route('reminders.index') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Nama Reminder:</th>
                                <td>{{ $reminder->name }}</td>
                            </tr>
                            <tr>
                                <th>Produk:</th>
                                <td>
                                    @if($reminder->product)
                                        <span class="badge bg-primary">{{ $reminder->product->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Semua Produk</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Hari Setelah Delivery:</th>
                                <td><span class="badge bg-info">{{ $reminder->days_after_delivery }} Hari</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Status:</th>
                                <td>
                                    @if($reminder->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Dibuat:</th>
                                <td>{{ $reminder->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Update:</th>
                                <td>{{ $reminder->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <h6 class="fw-bold">Template Pesan:</h6>
                    <div class="alert alert-light border">
                        <pre class="mb-0" style="white-space: pre-wrap;">{{ $reminder->message_template }}</pre>
                    </div>
                    <small class="text-muted">
                        <strong>Variable:</strong> 
                        <code>{customer_name}</code>, 
                        <code>{product_name}</code>, 
                        <code>{order_date}</code>, 
                        <code>{days_since}</code>
                    </small>
                </div>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary">{{ $stats['total'] }}</h3>
                        <small class="text-muted">Total Log</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success">{{ $stats['sent'] }}</h3>
                        <small class="text-muted">Terkirim</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning">{{ $stats['pending'] }}</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-danger">{{ $stats['failed'] }}</h3>
                        <small class="text-muted">Gagal</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Log Pengiriman --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Pengiriman</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Order ID</th>
                                <th>Scheduled</th>
                                <th>Sent</th>
                                <th>Status</th>
                                <th>Pesan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>
                                    <strong>{{ $log->customer->name }}</strong><br>
                                    <small class="text-muted">{{ $log->customer->phone }}</small>
                                </td>
                                <td>#{{ $log->order_id }}</td>
                                <td>{{ $log->scheduled_at->format('d M Y H:i') }}</td>
                                <td>
                                    @if($log->sent_at)
                                        {{ $log->sent_at->format('d M Y H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->status === 'sent')
                                        <span class="badge bg-success">Terkirim</span>
                                    @elseif($log->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Gagal</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->message_sent)
                                        <button class="btn btn-sm btn-outline-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#messageModal{{ $log->id }}">
                                            <i class="bi bi-eye"></i> Lihat
                                        </button>

                                        {{-- Modal --}}
                                        <div class="modal fade" id="messageModal{{ $log->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h6 class="modal-title">Pesan yang Dikirim</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <pre style="white-space: pre-wrap;">{{ $log->message_sent }}</pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada log pengiriman
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($logs->hasPages())
                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
