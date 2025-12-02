@extends('layout.main')

@section('title', 'Jadwal Pengingat (Reminder)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Manajemen Reminder</h2>
        <p class="text-muted">Kelola aturan pengingat dan pantau jadwal pengiriman.</p>
    </div>
    <a href="{{ route('reminders.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Buat Rule Reminder
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Section 1: Aturan Reminder --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-gear-fill me-2"></i>Aturan Reminder Aktif</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Nama Rule</th>
                        <th class="py-3">Produk Pemicu</th>
                        <th class="py-3">Waktu Kirim</th>
                        <th class="py-3">Status</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $rule->name }}</td>
                        <td>
                            @if($rule->product)
                                <span class="badge bg-info text-dark">{{ $rule->product->name }}</span>
                            @else
                                <span class="badge bg-secondary">Semua Produk</span>
                            @endif
                        </td>
                        <td>
                            {{ $rule->days_after_delivery }} hari setelah delivered
                        </td>
                        <td>
                            @if($rule->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('reminders.edit', $rule->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada aturan reminder. Silakan buat baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Section 2: Log Jadwal --}}
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-secondary"><i class="bi bi-calendar-event me-2"></i>Log Jadwal Pengiriman</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Pelanggan</th>
                        <th class="py-3">Order Terkait</th>
                        <th class="py-3">Jadwal Kirim</th>
                        <th class="py-3">Pesan</th>
                        <th class="py-3">Status</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reminders as $reminder)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-primary">{{ $reminder->customer->name }}</div>
                            <small class="text-muted">{{ $reminder->customer->phone }}</small>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $reminder->order_id) }}" class="text-decoration-none badge bg-light text-dark border">
                                {{ $reminder->order->order_number }}
                            </a>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold {{ $reminder->scheduled_at->isPast() && $reminder->status == 'pending' ? 'text-danger' : '' }}">
                                    {{ $reminder->scheduled_at->format('d M Y') }}
                                </span>
                                <small class="text-muted">{{ $reminder->scheduled_at->format('H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $reminder->reminder->message_template }}">
                                {{ $reminder->reminder->message_template }}
                            </span>
                        </td>
                        <td>
                            @if($reminder->status == 'sent')
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Terkirim</span>
                            @elseif($reminder->status == 'failed')
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Gagal</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Menunggu</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            @if($reminder->status == 'pending')
                            <form action="{{ route('reminders.destroy', $reminder->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pengingat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" alt="Empty" style="width: 60px; opacity: 0.5;">
                            <p class="text-muted mt-3">Belum ada jadwal pengingat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reminders->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $reminders->links() }}
    </div>
    @endif
</div>
@endsection