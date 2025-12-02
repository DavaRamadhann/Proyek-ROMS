@extends('layout.main')

@section('title', 'Chat Inbox - ROMS')

@section('search-placeholder', 'Cari percakapan atau pesan...')

@section('topbar-actions')
<button class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i><span class="d-none d-lg-inline">Chat Baru</span>
</button>
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
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
        border-left: 4px solid #0d6efd;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .chat-item {
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s;
        text-decoration: none;
        display: block;
    }
    .chat-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    .chat-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('main-content')

{{-- WhatsApp Connection Alert --}}
{{-- WhatsApp Connection Alert --}}
@if(isset($isConnected) && !$isConnected)
<div class="alert alert-warning d-flex align-items-center" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
    <div class="flex-grow-1">
        <h6 class="alert-heading mb-1">WhatsApp Belum Terhubung</h6>
        <p class="mb-0">Untuk menggunakan fitur chat, Anda perlu menghubungkan akun WhatsApp terlebih dahulu.</p>
    </div>
    <a href="{{ route('whatsapp.scan') }}" class="btn btn-warning text-dark fw-bold">
        <i class="bi bi-qr-code-scan me-2"></i>Scan QR Code
    </a>
</div>
@endif

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h3 class="mb-1 fw-bold"><i class="bi bi-chat-dots-fill me-2"></i>Chat Inbox</h3>
        <p class="mb-0 opacity-75">Daftar percakapan dengan pelanggan</p>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6">
        <div class="stats-card">
            <small class="text-muted">Total Chat</small>
            <h4 class="mb-0 fw-bold" style="color: #0d6efd;">{{ $rooms->count() }}</h4>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="stats-card" style="border-left-color: #dc3545;">
            <small class="text-muted">Chat Baru</small>
            <h4 class="mb-0 fw-bold" style="color: #dc3545;">{{ $rooms->where('status', 'new')->count() }}</h4>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="stats-card" style="border-left-color: #198754;">
            <small class="text-muted">Open</small>
            <h4 class="mb-0 fw-bold" style="color: #198754;">{{ $rooms->where('status', 'open')->count() }}</h4>
        </div>
    </div>
</div>

<div class="card data-card border-0">
    <div class="card-body p-0">
        @forelse ($rooms as $room)
            <a href="{{ route('chat.show', $room->id) }}" class="chat-item" id="chat-item-{{ $room->id }}">
                <div class="d-flex w-100 justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; font-weight: 700;">
                                {{ strtoupper(substr($room->customer->name, 0, 2)) }}
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $room->customer->name }}</h5>
                                <small class="text-muted">
                                    <i class="bi bi-telephone-fill me-1"></i>{{ $room->customer->phone }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-clock me-1"></i>{{ $room->updated_at->diffForHumans() }}
                        </small>
                        @if ($room->status == 'new')
                            <span class="badge bg-danger"><i class="bi bi-bell-fill me-1"></i>BARU</span>
                        @elseif ($room->status == 'open')
                            <span class="badge bg-primary"><i class="bi bi-chat-fill me-1"></i>Open</span>
                        @else
                            <span class="badge bg-secondary">{{ $room->status }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="p-5 text-center">
                <div class="mb-3">
                    <i class="bi bi-chat-square-text" style="font-size: 4rem; color: #dee2e6;"></i>
                </div>
                <h5 class="text-muted">Inbox Kosong</h5>
                <p class="text-muted mb-0">Belum ada percakapan yang tersedia</p>
            </div>
        @endforelse
    </div>
</div>

@endsection