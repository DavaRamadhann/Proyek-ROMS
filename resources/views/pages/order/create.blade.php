@extends('layouts.app')

@section('title', 'Buat Pesanan Baru')

@section('content')

    {{-- HEADER PAGE (TETAP DIAM) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 flex-none">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="plus-circle" class="h-6 w-6 text-[#B45253]"></i> Buat Pesanan Baru
            </h1>
            <p class="text-sm text-slate-500 mt-1">Isi formulir di bawah untuk membuat pesanan manual.</p>
        </div>
        
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    {{-- FORM WRAPPER (SCROLLABLE AREA) --}}
    {{-- PERBAIKAN: Menggunakan h-[calc(100vh-150px)] agar tinggi pas layar dan tidak scroll parent --}}
    <div class="w-full bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col h-[calc(100vh-160px)] overflow-hidden">
        <form action="{{ route('orders.store') }}" method="POST" class="flex flex-col h-full">
            @csrf
            
            {{-- AREA KONTEN YANG BISA DI-SCROLL --}}
            <div class="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar">
                
                {{-- SECTION 1: DATA PELANGGAN --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100 sticky top-0 bg-white z-10">
                        <i data-lucide="user" class="h-5 w-5 text-[#84994F]"></i> Data Pelanggan
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Pilih Pelanggan <span class="text-red-500">*</span></label>
                            <select name="customer_id" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                                <option value="" disabled selected>-- Cari Pelanggan --</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                            <div class="mt-2 text-xs text-slate-400 flex items-center gap-1">
                                <i data-lucide="info" class="h-3 w-3"></i> Pelanggan belum ada? <a href="{{ route('customers.create') }}" class="text-[#84994F] hover:underline font-bold">Tambah Baru</a>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Tanggal Order</label>
                            <input type="date" name="created_at" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: ITEM PESANAN --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                        <i data-lucide="shopping-cart" class="h-5 w-5 text-[#FCB53B]"></i> Item Pesanan
                    </h3>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-sm" id="itemsTable">
                            <thead class="bg-slate-100 text-slate-600 font-bold uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Produk</th>
                                    <th class="px-4 py-3 w-24 text-center">Qty</th>
                                    <th class="px-4 py-3 w-16 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200" id="itemsContainer">
                                {{-- Item Row Template --}}
                                <tr class="item-row">
                                    <td class="px-4 py-2">
                                        <select name="items[0][product_id]" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F]">
                                            <option value="" disabled selected>-- Pilih Produk --</option>
                                            @foreach($products ?? [] as $product)
                                                <option value="{{ $product->id }}">
                                                    {{ $product->name }} (Stok: {{ $product->stock }}) - Rp {{ number_format($product->price, 0, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="items[0][quantity]" value="1" min="1" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-center focus:outline-none focus:border-[#84994F]">
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" class="remove-row-btn p-2 text-slate-400 hover:text-red-500 transition disabled:opacity-50" disabled>
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="p-3 bg-slate-100 border-t border-slate-200">
                            <button type="button" id="addItemBtn" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                                <i data-lucide="plus" class="h-3 w-3"></i> Tambah Item Lain
                            </button>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: CATATAN --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Catatan Pesanan (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Titip di pos satpam, bungkus kado, dll..."
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition resize-none"></textarea>
                </div>
                
                {{-- Spacer Bawah agar tidak mepet saat discroll mentok --}}
                <div class="h-8"></div>

            </div>

            {{-- FOOTER TETAP (FIXED ACTIONS) --}}
            <div class="flex-none flex justify-end gap-3 p-4 bg-white border-t border-slate-100 z-20">
                <a href="{{ route('orders.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg font-bold text-sm hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#B45253] text-white rounded-lg font-bold text-sm hover:bg-[#9a4243] shadow-md shadow-red-100 transition flex items-center gap-2">
                    <i data-lucide="save" class="h-4 w-4"></i> Simpan Pesanan
                </button>
            </div>

        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();

            const container = document.getElementById('itemsContainer');
            const addBtn = document.getElementById('addItemBtn');
            let itemIndex = 1;

            addBtn.addEventListener('click', function() {
                const firstRow = container.querySelector('.item-row');
                const newRow = firstRow.cloneNode(true);
                
                const select = newRow.querySelector('select');
                const input = newRow.querySelector('input');
                
                select.name = `items[${itemIndex}][product_id]`;
                select.value = "";
                input.name = `items[${itemIndex}][quantity]`;
                input.value = 1;

                const deleteBtn = newRow.querySelector('.remove-row-btn');
                deleteBtn.disabled = false;
                deleteBtn.onclick = function() {
                    newRow.remove();
                };

                container.appendChild(newRow);
                lucide.createIcons();
                itemIndex++;
            });
        });
    </script>
    @endpush

@endsection