@extends('layouts.app')

@section('title', 'Laporan Bisnis - ROMS')

@push('styles')
<style>
    /* Disable scrolling on reports page */
    body {
        overflow: hidden !important;
        height: 100vh !important;
    }
    
    .reports-container {
        overflow: hidden !important;
        height: calc(100vh - 80px) !important;
    }
</style>
@endpush

@section('content')
<div class="reports-container">

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 flex items-center gap-3 tracking-tight">
                <div class="p-2 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl border border-emerald-100 shadow-sm">
                    <i data-lucide="bar-chart-3" class="h-8 w-8 text-emerald-600"></i>
                </div>
                Laporan & Analitik
            </h1>
            <p class="text-slate-500 mt-2 text-base font-medium ml-1">Wawasan mendalam untuk pertumbuhan bisnis Anda.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-slate-200 shadow-sm text-sm text-slate-600 font-medium">
                <i data-lucide="calendar" class="h-4 w-4 text-slate-400"></i>
                <span>{{ now()->format('d M Y') }}</span>
            </div>
            <a href="{{ route('admin.reports.export-pdf') }}" class="group bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-slate-200 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <i data-lucide="file-text" class="h-4 w-4 text-slate-300 group-hover:text-white transition-colors"></i> 
                Export PDF
            </a>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1: Total Pendapatan -->
        <div class="relative overflow-hidden bg-white p-6 rounded-2xl border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all duration-300 group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i data-lucide="dollar-sign" class="h-24 w-24 text-emerald-600 transform translate-x-4 -translate-y-4"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                        <i data-lucide="wallet" class="h-6 w-6"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pendapatan</span>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <div class="flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 w-fit px-2 py-1 rounded-full">
                    <i data-lucide="trending-up" class="h-3 w-3"></i>
                    <span>Total Akumulasi</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Pesanan -->
        <div class="relative overflow-hidden bg-white p-6 rounded-2xl border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all duration-300 group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i data-lucide="shopping-bag" class="h-24 w-24 text-blue-600 transform translate-x-4 -translate-y-4"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                        <i data-lucide="shopping-cart" class="h-6 w-6"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Total Pesanan</span>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800 mb-1">{{ number_format($totalOrders) }}</h3>
                <div class="flex items-center gap-1 text-xs font-medium text-blue-600 bg-blue-50 w-fit px-2 py-1 rounded-full">
                    <i data-lucide="package-check" class="h-3 w-3"></i>
                    <span>Transaksi Berhasil</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Pelanggan -->
        <div class="relative overflow-hidden bg-white p-6 rounded-2xl border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all duration-300 group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i data-lucide="users" class="h-24 w-24 text-indigo-600 transform translate-x-4 -translate-y-4"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pelanggan</span>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800 mb-1">{{ number_format($totalCustomers) }}</h3>
                <div class="flex items-center gap-1 text-xs font-medium text-indigo-600 bg-indigo-50 w-fit px-2 py-1 rounded-full">
                    <i data-lucide="user-check" class="h-3 w-3"></i>
                    <span>Terdaftar</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Rata-rata Order -->
        <div class="relative overflow-hidden bg-white p-6 rounded-2xl border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all duration-300 group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i data-lucide="pie-chart" class="h-24 w-24 text-amber-500 transform translate-x-4 -translate-y-4"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                        <i data-lucide="bar-chart-4" class="h-6 w-6"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Avg. Order</span>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</h3>
                <div class="flex items-center gap-1 text-xs font-medium text-amber-600 bg-amber-50 w-fit px-2 py-1 rounded-full">
                    <i data-lucide="activity" class="h-3 w-3"></i>
                    <span>Per Transaksi</span>
                </div>
            </div>
        </div>
    </div>

    {{-- GRID KONTEN TENGAH --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        {{-- TABEL: TOP 10 PELANGGAN --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-100 flex flex-col overflow-hidden h-full">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-50 rounded-lg text-amber-500">
                        <i data-lucide="trophy" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Top 10 Pelanggan Loyal</h3>
                        <p class="text-xs text-slate-500">Pelanggan dengan total belanja tertinggi</p>
                    </div>
                </div>

            </div>
            <div class="flex-1 overflow-auto custom-scrollbar">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/50 text-slate-500 font-bold uppercase text-xs sticky top-0 backdrop-blur-sm">
                        <tr>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4 text-center">Jml Order</th>
                            <th class="px-6 py-4 text-right">Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($topCustomers as $index => $data)
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 text-slate-600 flex items-center justify-center text-sm font-bold shadow-sm group-hover:scale-110 transition-transform duration-300 border border-white ring-2 ring-slate-50">
                                            {{ substr($data->customer->name ?? 'U', 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-700 group-hover:text-blue-600 transition">{{ $data->customer->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-slate-400 flex items-center gap-1">
                                            <i data-lucide="phone" class="h-3 w-3"></i>
                                            {{ $data->customer->phone ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100">
                                    {{ $data->order_count }} Order
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-emerald-600">Rp {{ number_format($data->total_spent, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-400 flex flex-col items-center gap-2">
                                <i data-lucide="inbox" class="h-8 w-8 text-slate-300"></i>
                                <span>Belum ada data transaksi.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CHART: SEBARAN GEOGRAFIS --}}
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-100 flex flex-col h-full overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center gap-3">
                <div class="p-2 bg-rose-50 rounded-lg text-rose-500">
                    <i data-lucide="map-pin" class="h-5 w-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Sebaran Wilayah</h3>
                    <p class="text-xs text-slate-500">Top 10 Kota Pelanggan</p>
                </div>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <div class="relative h-48 w-full mb-6">
                    <canvas id="geoChart"></canvas>
                </div>
                
                <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-3">
                    @php $maxCityCount = $geoDistribution->max('total') ?? 1; @endphp
                    @forelse($geoDistribution as $geo)
                    @php $percent = ($geo->total / $maxCityCount) * 100; @endphp
                    <div class="group">
                        <div class="flex justify-between items-center mb-1">
                            <span class="flex items-center gap-2 text-sm font-medium text-slate-600">
                                <i data-lucide="building-2" class="h-3.5 w-3.5 text-slate-400"></i>
                                {{ $geo->city ?: 'Kota Tidak Diketahui' }}
                            </span>
                            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                                {{ $geo->total }}
                            </span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-rose-500 rounded-full opacity-60 group-hover:opacity-100 transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-slate-400 text-sm py-8">Belum ada data kota.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- CARD GRID: TOP 10 PRODUK TERLARIS --}}
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-100 overflow-hidden mb-10">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-500">
                    <i data-lucide="package" class="h-5 w-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Top 10 Produk Terlaris</h3>
                    <p class="text-xs text-slate-500">Berdasarkan total pendapatan penjualan</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                <span>All Time</span>
            </div>
        </div>

        @if($topProducts->isEmpty())
            <div class="p-16 text-center flex flex-col items-center justify-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="inbox" class="h-10 w-10 text-slate-300"></i>
                </div>
                <h4 class="text-slate-600 font-bold mb-1">Belum ada data produk</h4>
                <p class="text-slate-400 text-sm">Data penjualan produk akan muncul di sini.</p>
            </div>
        @else
            @php
                $maxRevenue = $topProducts->max('total_revenue');
            @endphp
            
            <div class="p-6">
                <div class="mb-6 h-64 w-full">
                    <canvas id="productChart"></canvas>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($topProducts as $index => $product)
                        @php
                            // Calculate percentage for progress bar
                            $percentage = $maxRevenue > 0 ? ($product->total_revenue / $maxRevenue) * 100 : 0;
                            
                            // Determine ranking style
                            $rankColor = 'bg-slate-100 text-slate-500';
                            $barColor = 'bg-indigo-500';
                            
                            if ($index === 0) {
                                $rankColor = 'bg-yellow-100 text-yellow-600 ring-1 ring-yellow-200';
                                $barColor = 'bg-yellow-500';
                            } elseif ($index === 1) {
                                $rankColor = 'bg-slate-200 text-slate-600 ring-1 ring-slate-300';
                                $barColor = 'bg-slate-400';
                            } elseif ($index === 2) {
                                $rankColor = 'bg-orange-100 text-orange-600 ring-1 ring-orange-200';
                                $barColor = 'bg-orange-500';
                            }
                        @endphp
                        
                        <div class="group relative bg-white border border-slate-100 rounded-xl p-4 hover:border-indigo-100 hover:shadow-md transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-lg {{ $rankColor }} flex items-center justify-center text-sm font-bold">
                                        #{{ $index + 1 }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="font-bold text-slate-800 text-sm truncate pr-2 group-hover:text-indigo-600 transition">
                                            {{ $product->name }}
                                        </h4>
                                        <span class="text-sm font-bold text-emerald-600 whitespace-nowrap">
                                            Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center gap-3 text-xs text-slate-500 mb-3">
                                        <span class="flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">
                                            <i data-lucide="package" class="h-3 w-3"></i>
                                            {{ number_format($product->total_qty) }} terjual
                                        </span>
                                        <span class="text-slate-300">|</span>
                                        <span>{{ number_format($percentage, 1) }}% dari top product</span>
                                    </div>

                                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full transition-all duration-1000 ease-out opacity-80 group-hover:opacity-100" 
                                             style="width: {{ $percentage }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Re-init Lucide Icons
            if (typeof lucide !== 'undefined') lucide.createIcons();

            // Setup Chart
            const ctx = document.getElementById('geoChart').getContext('2d');
            
            // Mengambil data dari PHP Blade variable
            const labels = {!! json_encode($geoDistribution->pluck('city')->map(fn($c) => $c ?: 'N/A')) !!};
            const data = {!! json_encode($geoDistribution->pluck('total')) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Pelanggan',
                        data: data,
                        backgroundColor: 'rgba(180, 82, 83, 0.2)', // Warna Maroon Transparan (#B45253)
                        borderColor: '#B45253', // Warna Maroon Solid
                        borderWidth: 1,
                        borderRadius: 4,
                        barThickness: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: true, color: '#f1f5f9' },
                            ticks: { stepSize: 1, font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8
                        }
                    }
                }
            });

            // Setup Product Chart
            const ctxProduct = document.getElementById('productChart').getContext('2d');
            const productLabels = {!! json_encode($topProducts->pluck('name')) !!};
            const productData = {!! json_encode($topProducts->pluck('total_revenue')) !!};

            new Chart(ctxProduct, {
                type: 'bar',
                data: {
                    labels: productLabels,
                    datasets: [{
                        label: 'Total Pendapatan',
                        data: productData,
                        backgroundColor: 'rgba(99, 102, 241, 0.2)', // Indigo 500 transparent
                        borderColor: '#6366f1', // Indigo 500
                        borderWidth: 1,
                        borderRadius: 4,
                        barThickness: 30
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { display: true, color: '#f1f5f9' },
                            ticks: { 
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                },
                                font: { size: 10 } 
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.x !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.x);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</div>

@endsection