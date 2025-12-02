@extends('layout.cs_main') {{-- PENTING: Menggunakan layout CS baru --}}

@section('title', 'Beranda CS - ROMS')

@push('styles')
<style>
    /* Style ini mirip dengan dashboard admin, tapi pakai palet Anda */
    .dashboard-header {
        font-size: 1.75rem;
        font-weight: 700;
        color: #333;
    }
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        height: 100%; 
    }
    .info-card .info-value {
        font-size: 1.75rem;
        font-weight: 700;
    }
    .saldo-label {
        font-size: 0.9rem;
        color: #6c757d;
    }
</style>
@endpush


@section('main-content')

<h2 class="dashboard-header mb-4">Dashboard Customer Service</h2>

<div class="row g-4">
    <div class="col-lg-4 col-md-6">
        <div class="card p-3 info-card">
            <span class="saldo-label">Obrolan Belum Dibaca</span>
            <h3 class="info-value text-danger">{{ $unreadChatsCount }}</h3>
            <small class="text-muted">Dari {{ $todayChatsCount }} total obrolan hari ini</small>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card p-3 info-card">
            <span class="saldo-label">Pesanan Perlu Diproses</span>
            <h3 class="info-value text-warning">{{ $pendingOrdersCount }}</h3>
            <small class="text-muted">Pesanan baru menunggu konfirmasi</small>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
        <div class="card p-3 info-card">
            <span class="saldo-label">Total Pelanggan Aktif</span>
            <h3 class="info-value">{{ $activeCustomersCount }}</h3>
            <small class="text-muted">Pelanggan yang pernah chat</small>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Obrolan Terbaru</h5>
        @if($recentChats->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="bi bi-chat-dots" style="font-size: 3rem;"></i>
                <p class="mt-2">Belum ada obrolan</p>
            </div>
        @else
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">Nama Pelanggan</th>
                        <th scope="col">Pesan Terakhir</th>
                        <th scope="col">Waktu</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentChats as $chat)
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('chat.ui') }}'">
                            <td>
                                <strong>{{ $chat['customer_name'] }}</strong><br>
                                <small class="text-muted">{{ $chat['customer_phone'] }}</small>
                            </td>
                            <td>{{ $chat['last_message'] }}</td>
                            <td>{{ $chat['last_message_time']->diffForHumans() }}</td>
                            <td>
                                @if($chat['status'] === 'unread')
                                    <span class="badge bg-danger">Belum Dibaca</span>
                                @elseif($chat['status'] === 'read')
                                    <span class="badge bg-secondary">Sudah Dibaca</span>
                                @elseif($chat['status'] === 'closed')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-info">{{ ucfirst($chat['status']) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection