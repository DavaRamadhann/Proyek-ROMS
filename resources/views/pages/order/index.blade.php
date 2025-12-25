@extends('layouts.app')

@section('title', 'Daftar Pesanan')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="shopping-cart" class="h-6 w-6 text-[#84994F]"></i> Daftar Pesanan
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola semua pesanan masuk dan status pengiriman.</p>
        </div>
        
        
        <div class="flex items-center gap-2">
            <a href="{{ route('orders.import.form') }}" class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center gap-2">
                <i data-lucide="upload" class="h-4 w-4"></i> Import Pesanan
            </a>
            <a href="{{ route('orders.create') }}" class="bg-[#B45253] hover:bg-[#9a4243] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-red-100 transition flex items-center gap-2">
                <i data-lucide="plus" class="h-4 w-4"></i> Buat Pesanan Baru
            </a>
        </div>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-2 text-sm font-medium animate-fade-in-down">
            <i data-lucide="check-circle-2" class="h-4 w-4"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- STATS CARDS --}}
    {{-- PERBAIKAN: Font size dikembalikan ke ukuran semula (text-xs & text-2xl) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        
        <!-- Card 1: Revenue (Gradient Green) -->
        <div class="rounded-xl p-5 shadow-sm text-white flex flex-col justify-between relative overflow-hidden bg-gradient-to-br from-[#84994F] to-[#6b7c40]">
            <div class="absolute right-0 top-0 -mt-2 -mr-2 w-20 h-20 bg-white opacity-10 rounded-full blur-xl"></div>
            <p class="text-xs font-bold uppercase tracking-widest text-green-100 opacity-80">Total Pendapatan</p>
            <div class="mt-2">
                <h3 class="text-2xl font-bold">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</h3>
            </div>
            <div class="mt-2 text-[10px] bg-white/20 inline-flex px-2 py-0.5 rounded text-white font-medium w-fit">
                Bulan Ini
            </div>
        </div>

        <!-- Card 2: Total Pesanan -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pesanan</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total'] }}</h3>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <i data-lucide="receipt" class="h-5 w-5"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Pending -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm border-l-4 border-[#FCB53B] flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pending</p>
                    <h3 class="text-2xl font-bold text-[#FCB53B] mt-1">{{ $stats['pending'] }}</h3>
                </div>
                <div class="p-2 bg-[#FCB53B]/10 text-[#FCB53B] rounded-lg">
                    <i data-lucide="hourglass" class="h-5 w-5"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Selesai -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm border-l-4 border-[#84994F] flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Selesai</p>
                    <h3 class="text-2xl font-bold text-[#84994F] mt-1">{{ $stats['completed'] }}</h3>
                </div>
                <div class="p-2 bg-[#84994F]/10 text-[#84994F] rounded-lg">
                    <i data-lucide="check-circle" class="h-5 w-5"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="mb-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('orders.index', ['status' => 'all']) }}" 
               class="px-4 py-1.5 rounded-full text-xs font-bold transition border {{ request('status') == 'all' || !request('status') ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                Semua
            </a>

            <a href="{{ route('orders.index', ['status' => 'pending']) }}" 
               class="flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold transition border {{ request('status') == 'pending' ? 'bg-[#FCB53B] text-white border-[#FCB53B]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                Pending 
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') == 'pending' ? 'bg-white/30 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $stats['pending'] }}</span>
            </a>

            <a href="{{ route('orders.index', ['status' => 'shipped']) }}" 
               class="flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold transition border {{ request('status') == 'shipped' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                Dikirim 
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') == 'shipped' ? 'bg-white/30 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $stats['shipped'] }}</span>
            </a>

            <a href="{{ route('orders.index', ['status' => 'completed']) }}" 
               class="flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold transition border {{ request('status') == 'completed' ? 'bg-[#84994F] text-white border-[#84994F]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                Selesai 
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') == 'completed' ? 'bg-white/30 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $stats['completed'] }}</span>
            </a>

            <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" 
               class="flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold transition border {{ request('status') == 'cancelled' ? 'bg-[#B45253] text-white border-[#B45253]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                Dibatalkan 
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') == 'cancelled' ? 'bg-white/30 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $stats['cancelled'] }}</span>
            </a>
        </div>
    </div>

    {{-- ORDERS TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-10 w-full">
        <div class="max-h-[600px] overflow-y-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold tracking-wider sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 w-[15%]">No. Order</th>
                        <th class="px-4 py-3 w-[25%]">Pelanggan</th>
                        <th class="px-4 py-3 w-[15%]">Total</th>
                        <th class="px-4 py-3 w-[15%]">Status</th>
                        <th class="px-4 py-3 w-[15%]">Tanggal</th>
                        <th class="px-4 py-3 w-[15%] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50 transition group">
                            
                            {{-- No Order --}}
                            <td class="px-4 py-3">
                                <a href="{{ route('orders.show', $order->id) }}" class="font-bold text-blue-600 hover:underline hover:text-blue-700 font-mono">
                                    {{ $order->order_number }}
                                </a>
                            </td>

                            {{-- Pelanggan --}}
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-700 truncate max-w-[200px]">{{ $order->customer->name }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                    <i data-lucide="phone" class="h-3 w-3"></i> {{ $order->customer->phone }}
                                </div>
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-3 font-bold text-[#84994F]">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>

                            {{-- Status Dropdown --}}
                            <td class="px-4 py-3">
                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                                    @csrf
                                    @php
                                        $bgClass = 'bg-slate-100 text-slate-600';
                                        if($order->status == 'completed') $bgClass = 'bg-green-50 text-green-700 border-green-200';
                                        elseif($order->status == 'pending') $bgClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                        elseif($order->status == 'shipped') $bgClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                        elseif($order->status == 'cancelled') $bgClass = 'bg-red-50 text-red-700 border-red-200';
                                    @endphp

                                    <select name="status" onchange="this.form.submit()" 
                                        class="text-xs font-bold px-2 py-1.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-opacity-50 transition cursor-pointer {{ $bgClass }} border-transparent hover:border-current focus:ring-slate-300 w-full max-w-[120px]">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </form>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-3 text-slate-500 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="h-3.5 w-3.5"></i>
                                    {{ $order->created_at->format('d M Y') }}
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('chat.ui', ['customer_id' => $order->customer_id]) }}" 
                                       class="p-1.5 bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-700 rounded-lg transition" title="Chat Customer">
                                        <i data-lucide="message-circle" class="h-4 w-4"></i>
                                    </a>
                                    <a href="{{ route('orders.show', $order->id) }}" 
                                       class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-lg hover:border-[#84994F] hover:text-[#84994F] transition shadow-sm">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="bg-slate-50 p-4 rounded-full">
                                        <i data-lucide="shopping-bag" class="h-8 w-8 opacity-20 text-slate-500"></i>
                                    </div>
                                    <p class="font-medium text-slate-600">Belum ada pesanan.</p>
                                    <p class="text-xs text-slate-400">Pesanan baru akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="p-3 border-t border-slate-50 bg-slate-50/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
    @endpush

@endsection