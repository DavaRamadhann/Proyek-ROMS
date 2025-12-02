@extends('layout.main')

@section('title', 'Laporan Bisnis - ROMS')

@section('main-content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">Laporan & Analitik</h2>
            <p class="text-muted">Wawasan bisnis untuk pengambilan keputusan strategis.</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- 1. Top 10 Pelanggan --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-trophy-fill me-2"></i>Top 10 Pelanggan Loyal</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Pelanggan</th>
                                    <th class="text-center">Jml Order</th>
                                    <th class="text-end pe-4">Total Belanja</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers as $data)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $data->customer->name ?? 'Unknown' }}</div>
                                        <small class="text-muted">{{ $data->customer->phone ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark rounded-pill">{{ $data->order_count }}</span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-success">
                                        Rp {{ number_format($data->total_spent, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada data transaksi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Sebaran Geografis --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-danger"><i class="bi bi-geo-alt-fill me-2"></i>Sebaran Wilayah (Top 10)</h5>
                </div>
                <div class="card-body">
                    {{-- Placeholder Chart --}}
                    <div class="mb-3">
                        <canvas id="geoChart" height="200"></canvas>
                    </div>

                    <ul class="list-group list-group-flush">
                        @forelse($geoDistribution as $geo)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>
                                <i class="bi bi-building text-muted me-2"></i>
                                {{ $geo->city ?: 'Kota Tidak Diketahui' }}
                            </span>
                            <span class="badge bg-secondary rounded-pill">{{ $geo->total }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">Belum ada data kota.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('geoChart').getContext('2d');
        
        // Data dari PHP
        const labels = {!! json_encode($geoDistribution->pluck('city')->map(fn($c) => $c ?: 'N/A')) !!};
        const data = {!! json_encode($geoDistribution->pluck('total')) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pelanggan',
                    data: data,
                    backgroundColor: 'rgba(220, 53, 69, 0.6)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
