@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')

    {{-- HEADER PAGE WITH GRADIENT --}}
    <div class="mb-5 rounded-2xl bg-gradient-to-br from-[#FCB53B] to-[#e09d22] p-6 shadow-lg md:p-8 text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold flex items-center gap-2">
                    <i data-lucide="plus-circle" class="h-6 w-6"></i> Tambah Produk
                </h2>
                <p class="mt-1 text-white/90">Tambahkan produk baru ke dalam inventori.</p>
            </div>
        </div>
        
        {{-- Custom Breadcrumb --}}
        <div class="mt-4 inline-flex rounded-lg bg-white/20 px-4 py-2 backdrop-blur-sm border border-white/10">
            <nav class="flex text-sm text-white" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center hover:text-white/80 transition-colors">
                            <i data-lucide="home" class="mr-2 h-3.5 w-3.5"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="h-4 w-4 text-white/60"></i>
                            <a href="{{ route('product.index') }}" class="ml-1 hover:text-white/80 md:ml-2 transition-colors">Produk</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="h-4 w-4 text-white/60"></i>
                            <span class="ml-1 font-bold md:ml-2">Tambah Baru</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- FORM CARD --}}
    <div class="flex justify-center pb-20">
        <div class="w-full">
            <div class="overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md">
                
                {{-- Card Header --}}
                <div class="border-b-4 border-[#FCB53B] bg-slate-50 px-6 py-4 flex items-center gap-2">
                    <div class="p-2 bg-[#FCB53B]/10 rounded-lg text-[#FCB53B]">
                        <i data-lucide="package-plus" class="h-5 w-5"></i>
                    </div>
                    <h5 class="font-bold text-slate-700">Form Data Produk</h5>
                </div>

                {{-- Card Body --}}
                <div class="p-6 md:p-8">
                    <form action="{{ route('product.store') }}" method="POST">
                        @csrf
                        
                        {{-- Nama Produk --}}
                        <div class="mb-5">
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" class="w-full rounded-lg border @error('name') border-red-500 @else border-slate-200 @enderror px-4 py-2.5 text-sm text-slate-700 focus:border-[#FCB53B] focus:outline-none focus:ring-2 focus:ring-[#FCB53B]/50 transition duration-200" placeholder="Contoh: Kopi Arabika 500g" value="{{ old('name') }}" required>
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SKU --}}
                        <div class="mb-5">
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                SKU (Stock Keeping Unit) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="sku" class="w-full rounded-lg border @error('sku') border-red-500 @else border-slate-200 @enderror px-4 py-2.5 text-sm text-slate-700 focus:border-[#FCB53B] focus:outline-none focus:ring-2 focus:ring-[#FCB53B]/50 transition duration-200" placeholder="Contoh: KOP-ARB-500" value="{{ old('sku') }}" required>
                            @error('sku')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-slate-400">Kode unik untuk identifikasi stok produk.</p>
                            @enderror
                        </div>

                        {{-- Grid Harga & Stok --}}
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-5">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">
                                    Harga (Rp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-slate-400 text-sm">Rp</span>
                                    <input type="number" name="price" class="w-full rounded-lg border @error('price') border-red-500 @else border-slate-200 @enderror pl-10 pr-4 py-2.5 text-sm text-slate-700 focus:border-[#FCB53B] focus:outline-none focus:ring-2 focus:ring-[#FCB53B]/50 transition duration-200" placeholder="0" value="{{ old('price') }}" required>
                                    @error('price')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">
                                    Stok Awal <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="stock" class="w-full rounded-lg border @error('stock') border-red-500 @else border-slate-200 @enderror px-4 py-2.5 text-sm text-slate-700 focus:border-[#FCB53B] focus:outline-none focus:ring-2 focus:ring-[#FCB53B]/50 transition duration-200" placeholder="0" value="{{ old('stock') }}" required>
                                @error('stock')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-5">
                            <label class="mb-2 block text-sm font-bold text-slate-700">Deskripsi</label>
                            <textarea name="description" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:border-[#FCB53B] focus:outline-none focus:ring-2 focus:ring-[#FCB53B]/50 transition duration-200 resize-none" rows="3" placeholder="Deskripsi singkat produk...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Rekomendasi Cross-sell --}}
                        <div class="mb-6">
                            <label class="mb-2 block text-sm font-bold text-slate-700">Rekomendasi Cross-sell (Opsional)</label>
                            <textarea name="recommendation_text" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:border-[#FCB53B] focus:outline-none focus:ring-2 focus:ring-[#FCB53B]/50 transition duration-200" rows="2" placeholder="Contoh: Cobain juga Kopi Robusta kami yang lebih strong!">{{ old('recommendation_text') }}</textarea>
                            <div class="mt-2 flex items-start gap-2 text-xs text-slate-400">
                                <i data-lucide="info" class="h-4 w-4 flex-shrink-0 mt-0.5"></i>
                                <span>Teks ini akan muncul otomatis di pesan reminder jika pelanggan membeli produk ini.</span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-8 flex items-center justify-between pt-6 border-t border-slate-100">
                            <a href="{{ route('product.index') }}" class="rounded-lg bg-slate-100 px-6 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-200 hover:text-slate-800 transition duration-200">
                                Batal
                            </a>
                            <button type="submit" class="rounded-lg bg-gradient-to-r from-[#FCB53B] to-[#e09d22] px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-orange-100 hover:shadow-lg hover:-translate-y-0.5 transition duration-200 flex items-center gap-2">
                                <i data-lucide="save" class="h-4 w-4"></i> Simpan Produk
                            </button>
                        </div>

                    </form>
                </div>
            </div>
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