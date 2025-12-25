@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- PAGE HEADER --}}
    <div class="flex-none mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div> 
            <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#84994F] shadow-sm border border-slate-100">
                <i data-lucide="layout-dashboard" class="h-3 w-3"></i><span>Ringkasan Bisnis</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">
                Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
            </h1>
            <p class="mt-1 text-slate-500">Berikut performa toko dan pesanan terbaru hari ini.</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Status WhatsApp / CS --}}
            @if(Auth::user()->role === 'cs')
                 <form action="{{ route('cs.status.toggle') }}" method="POST">
                    @csrf
                    <button class="flex items-center gap-3 rounded-xl bg-white px-4 py-2 shadow-sm border border-slate-100 hover:bg-slate-50 transition cursor-pointer">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ Auth::user()->is_online ? 'bg-[#84994F]' : 'bg-slate-400' }} opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 {{ Auth::user()->is_online ? 'bg-[#84994F]' : 'bg-slate-400' }}"></span>
                        </span>
                        <div>
                            <div class="text-[10px] font-bold uppercase text-slate-400">Status</div>
                            <div class="text-xs font-bold {{ Auth::user()->is_online ? 'text-[#84994F]' : 'text-slate-500' }}">
                                {{ Auth::user()->is_online ? 'ONLINE' : 'OFFLINE' }}
                            </div>
                        </div>
                    </button>
                </form>
            @else
                <div class="hidden sm:flex items-center gap-3 rounded-xl bg-white px-4 py-2 shadow-sm border border-slate-100">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ ($waStatus ?? 'DISCONNECTED') === 'CONNECTED' ? 'bg-[#84994F]' : 'bg-red-500' }} opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 {{ ($waStatus ?? 'DISCONNECTED') === 'CONNECTED' ? 'bg-[#84994F]' : 'bg-red-500' }}"></span>
                    </span>
                    <div>
                        <div class="text-[10px] font-bold uppercase text-slate-400">WhatsApp</div>
                        <div class="text-xs font-bold {{ ($waStatus ?? 'DISCONNECTED') === 'CONNECTED' ? 'text-[#84994F]' : 'text-red-500' }}">
                            {{ ($waStatus ?? 'DISCONNECTED') === 'CONNECTED' ? 'Connected' : 'Disconnected' }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-3 rounded-xl bg-white px-4 py-2 shadow-sm border border-slate-100">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#f1f5f9] text-[#84994F]">
                    <i data-lucide="calendar" class="h-4 w-4"></i>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase text-slate-400">Hari ini</div>
                    <div class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="flex-none grid gap-4 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm border-l-4 border-[#84994F] group hover:shadow-md transition-all">
             <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Omset</p>
                <div class="text-[#84994F] bg-[#84994F]/10 p-2 rounded-lg"><i data-lucide="banknote" class="h-5 w-5"></i></div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">
                Rp {{ number_format($stats['omset'] ?? 0, 0, ',', '.') }}
            </h3>
            <p class="text-[10px] text-[#84994F] font-bold mt-1 flex items-center gap-1">
                <i data-lucide="trending-up" class="h-3 w-3"></i> +12% vs bulan lalu
            </p>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm border-l-4 border-[#FCB53B] group hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Pesanan</p>
                <div class="text-[#FCB53B] bg-[#FCB53B]/10 p-2 rounded-lg"><i data-lucide="shopping-bag" class="h-5 w-5"></i></div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">
                {{ number_format($stats['total_order'] ?? 0) }}
            </h3>
            <p class="text-[10px] text-[#FCB53B] font-bold mt-1 flex items-center gap-1">
                {{ $stats['pesanan_hari_ini'] ?? 0 }} Pesanan baru
            </p>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm border-l-4 border-[#B45253] group hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-slate-500 uppercase">Pelanggan Aktif</p>
                <div class="text-[#B45253] bg-[#B45253]/10 p-2 rounded-lg"><i data-lucide="users" class="h-5 w-5"></i></div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">
                {{ number_format($stats['pelanggan_aktif'] ?? 0) }}
            </h3>
            <p class="text-[10px] text-slate-400 font-bold mt-1">Total database customer</p>
        </div>
    </div>

    {{-- CHART & TABLE --}}
    <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6 pb-6">
        
        <div class="lg:col-span-2 rounded-2xl bg-white p-5 shadow-sm border border-slate-100 flex flex-col h-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-slate-800">Trend Penjualan</h3>
                <button class="text-slate-400 hover:text-[#84994F]"><i data-lucide="more-horizontal" class="h-5 w-5"></i></button>
            </div>
            <div class="flex-1 border-b border-slate-50 pb-2 px-2 h-64 relative w-full">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-sm border border-slate-100 flex flex-col h-full overflow-hidden">
            <div class="p-5 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Pesanan Masuk</h3>
                <a href="{{ route('orders.index') }}" class="text-xs font-bold text-[#84994F] hover:underline">Semua</a>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-0">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 sticky top-0">
                        <tr class="text-[10px] uppercase text-slate-400 font-semibold">
                            <th class="py-2 px-4">Info</th>
                            <th class="py-2 px-4 text-right">Total</th>
                            <th class="py-2 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-50">
                        @forelse($recentOrders as $order)
                            @php
                                $statusClass = 'text-slate-500';
                                $s = strtolower($order->status);
                                if($s == 'completed' || $s == 'selesai') $statusClass = 'text-[#84994F]';
                                elseif($s == 'pending' || $s == 'dikemas') $statusClass = 'text-[#FCB53B]';
                                elseif($s == 'cancelled' || $s == 'batal') $statusClass = 'text-[#B45253]';
                            @endphp

                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-bold text-slate-700">
                                    #{{ $order->order_number ?? 'ORD-'.$order->id }}
                                    <div class="text-[10px] font-normal text-slate-500">
                                        {{ $order->customer->name ?? 'Pelanggan Umum' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right font-medium">
                                    Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right {{ $statusClass }} font-bold uppercase">
                                    {{ $order->status }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-400">
                                    Belum ada pesanan terbaru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesTrendChart').getContext('2d');
            
            const chartData = @json($stats['sales_trend']['data'] ?? []);
            const chartLabels = @json($stats['sales_trend']['labels'] ?? []);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Pesanan',
                        data: chartData,
                        borderColor: '#84994F',
                        backgroundColor: 'rgba(132, 153, 79, 0.1)',
                        borderWidth: 2,
                        tension: 0.4, // Smooth curves
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#84994F',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            titleFont: {
                                size: 13,
                                family: "'Plus Jakarta Sans', sans-serif"
                            },
                            bodyFont: {
                                size: 12,
                                family: "'Plus Jakarta Sans', sans-serif"
                            },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Pesanan';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    family: "'Plus Jakarta Sans', sans-serif"
                                },
                                color: '#64748b',
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    family: "'Plus Jakarta Sans', sans-serif"
                                },
                                color: '#64748b'
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
@endsection