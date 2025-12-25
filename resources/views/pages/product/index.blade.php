@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="package" class="h-6 w-6 text-[#84994F]"></i> Manajemen Produk
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola katalog produk, harga, dan stok inventori.</p>
        </div>
        
        <div class="flex gap-2">
            @if(auth()->user()->role === 'admin')
            {{-- PERBAIKAN: Menggunakan 'product.create' (tunggal) --}}
            <a href="{{ route('product.create') }}" class="bg-[#84994F] hover:bg-[#6b7d3f] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-green-100 transition flex items-center gap-2">
                <i data-lucide="plus-circle" class="h-4 w-4"></i> Tambah Produk
            </a>
            @endif
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        
        <!-- Card 1: Total Produk -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm border-l-4 border-[#FCB53B] flex flex-col justify-between">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Produk</p>
            <div class="flex items-center justify-between mt-2">
                <h3 class="text-2xl font-bold text-[#FCB53B]">{{ $products->total() }}</h3>
                <div class="p-2 bg-[#FCB53B]/10 rounded-lg text-[#FCB53B]">
                    <i data-lucide="box" class="h-5 w-5"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: SKU Unik -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm border-l-4 border-[#84994F] flex flex-col justify-between">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">SKU Unik</p>
            <div class="flex items-center justify-between mt-2">
                <h3 class="text-2xl font-bold text-[#84994F]">{{ $products->total() }}</h3>
                <div class="p-2 bg-[#84994F]/10 rounded-lg text-[#84994F]">
                    <i data-lucide="qr-code" class="h-5 w-5"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Stok Menipis -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm border-l-4 border-[#B45253] flex flex-col h-32">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Stok Menipis</p>
                <div class="p-1.5 bg-[#B45253]/10 rounded-lg text-[#B45253]">
                    <i data-lucide="alert-triangle" class="h-4 w-4"></i>
                </div>
            </div>
            
            <div class="overflow-y-auto pr-1">
                <ul class="space-y-2">
                    @forelse($lowStockProducts as $item)
                        <li class="flex items-center justify-between text-xs border-b border-slate-50 pb-1 last:border-0 last:pb-0">
                            <span class="text-slate-700 truncate w-2/3" title="{{ $item->name }}">{{ $item->name }}</span>
                            <span class="font-bold {{ $item->stock == 0 ? 'text-red-500' : 'text-[#B45253]' }}">{{ $item->stock }} unit</span>
                        </li>
                    @empty
                        <li class="text-xs text-slate-400 italic text-center py-4">Stok aman terkendali</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-2 text-sm font-medium">
            <i data-lucide="check-circle-2" class="h-4 w-4"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- DATA TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        
        {{-- Toolbar Pencarian --}}
        <div class="p-4 border-b border-slate-50 bg-slate-50/50">
            {{-- PERBAIKAN: Menggunakan 'product.index' (tunggal) --}}
            <form action="{{ route('product.index') }}" method="GET" class="relative w-full sm:w-72">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..." 
                    class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#84994F] focus:border-[#84994F] transition">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Nama Produk</th>
                        <th class="px-6 py-3">SKU (Kode)</th>
                        <th class="px-6 py-3 text-right">Harga</th>
                        <th class="px-6 py-3 text-center">Stok</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50 transition group">
                        
                        {{-- Nama Produk --}}
                        <td class="px-6 py-4 font-bold text-slate-700">
                            {{ $product->name }}
                        </td>

                        {{-- SKU --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-[#FCB53B]/10 text-[#e0a030] text-xs font-bold border border-[#FCB53B]/20 font-mono">
                                {{ $product->sku }}
                            </span>
                        </td>

                        {{-- Harga --}}
                        <td class="px-6 py-4 text-right font-medium text-slate-600">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>

                        {{-- Stok --}}
                        <td class="px-6 py-4 text-center">
                            @if($product->stock == 0)
                                <span class="text-red-500 font-bold text-xs bg-red-50 px-2 py-1 rounded-full">Habis</span>
                            @else
                                <span class="text-slate-700 font-bold">{{ $product->stock }}</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(auth()->user()->role === 'admin')
                                {{-- PERBAIKAN: Menggunakan 'product.edit' (tunggal) --}}
                                <a href="{{ route('product.edit', $product->id) }}" class="p-2 bg-white border border-slate-200 text-slate-400 hover:text-[#FCB53B] hover:border-[#FCB53B] rounded-lg transition shadow-sm" title="Edit">
                                    <i data-lucide="pencil" class="h-4 w-4"></i>
                                </a>
                                
                                {{-- PERBAIKAN: Menggunakan 'product.destroy' (tunggal) --}}
                                <form action="{{ route('product.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-white border border-slate-200 text-slate-400 hover:text-[#B45253] hover:border-[#B45253] rounded-lg transition shadow-sm" title="Hapus">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-slate-400 italic">Read Only</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-3">
                                <div class="bg-slate-50 p-4 rounded-full">
                                    <i data-lucide="package-open" class="h-8 w-8 opacity-20 text-slate-500"></i>
                                </div>
                                <p class="font-medium text-slate-600">Belum ada data produk.</p>
                                @if(auth()->user()->role === 'admin')
                                {{-- PERBAIKAN: Menggunakan 'product.create' (tunggal) --}}
                                <a href="{{ route('product.create') }}" class="text-[#84994F] font-bold text-xs hover:underline flex items-center gap-1">
                                    <i data-lucide="plus" class="h-3 w-3"></i> Tambah Produk Baru
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-slate-50 bg-slate-50/50">
             {{ $products->links() }}
        </div>
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