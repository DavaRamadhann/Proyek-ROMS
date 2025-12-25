@extends('layouts.app')

@section('title', 'Buat Reminder - ROMS')

@section('content')

    {{-- HEADER PAGE (Fixed) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 flex-none">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="bell-plus" class="h-6 w-6 text-[#FCB53B]"></i> Buat Reminder Baru
            </h1>
            <p class="text-sm text-slate-500 mt-1">Konfigurasi aturan pengingat otomatis untuk pelanggan.</p>
        </div>
        
        <a href="{{ route('reminders.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    {{-- FORM WRAPPER (Scrollable & Full Width) --}}
    <div class="w-full bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col h-[calc(100vh-160px)] overflow-hidden mb-10">
        
        <div class="border-b border-slate-50 px-6 py-4 flex items-center gap-2 bg-slate-50/50 flex-none sticky top-0 z-10">
            <div class="p-2 bg-[#FCB53B]/10 rounded-lg text-[#FCB53B]">
                <i data-lucide="settings-2" class="h-5 w-5"></i>
            </div>
            <h5 class="font-bold text-slate-700">Formulir Aturan Reminder</h5>
        </div>

        {{-- Form Content (Scrollable) --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar relative">
            <div class="p-6 md:p-8">
                
                {{-- ERROR ALERT --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <i data-lucide="alert-triangle" class="h-4 w-4"></i> Gagal menyimpan!
                        </div>
                        <ul class="list-disc list-inside text-sm opacity-90">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reminders.store') }}" method="POST" id="reminderForm">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        {{-- KOLOM KIRI: PENGATURAN DASAR --}}
                        <div>
                            <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Pengaturan Dasar</h6>
                            
                            {{-- Nama Reminder --}}
                            <div class="mb-5">
                                <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">Nama Reminder <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Reminder Kopi 25 Hari" required
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#FCB53B] focus:ring-1 focus:ring-[#FCB53B] transition">
                            </div>

                            {{-- Produk Pemicu --}}
                            <div class="mb-5">
                                <label for="product_id" class="block text-sm font-bold text-slate-700 mb-1.5">Produk Pemicu</label>
                                <select id="product_id" name="product_id" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#FCB53B] focus:ring-1 focus:ring-[#FCB53B] transition cursor-pointer">
                                    <option value="">-- Semua Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-slate-400">Reminder akan aktif jika pelanggan membeli produk ini.</p>
                            </div>

                            {{-- Hari Setelah Delivery --}}
                            <div class="mb-5">
                                <label for="days_after_delivery" class="block text-sm font-bold text-slate-700 mb-1.5">Jeda Waktu (Hari) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" id="days_after_delivery" name="days_after_delivery" value="{{ old('days_after_delivery', 30) }}" min="0" max="365" required
                                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#FCB53B] focus:ring-1 focus:ring-[#FCB53B] transition">
                                    <span class="absolute right-4 top-2.5 text-slate-400 text-sm font-medium">Hari setelah sampai</span>
                                </div>
                            </div>

                            {{-- Waktu Pengiriman --}}
                            <div class="mb-5">
                                <label for="send_time" class="block text-sm font-bold text-slate-700 mb-1.5">Waktu Pengiriman</label>
                                <input type="time" id="send_time" name="send_time" value="{{ old('send_time', '09:00') }}"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#FCB53B] focus:ring-1 focus:ring-[#FCB53B] transition">
                                <p class="mt-1 text-xs text-slate-400">Jam berapa pesan akan dikirim (WIB).</p>
                            </div>

                            {{-- Status Aktif (Switch) --}}
                            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="relative inline-block w-10 h-6 align-middle select-none transition duration-200 ease-in">
                                    <input type="checkbox" name="is_active" id="is_active" class="peer absolute block w-6 h-6 rounded-full bg-white border-4 border-slate-300 appearance-none cursor-pointer transition-all duration-300 checked:translate-x-full checked:border-[#84994F]" {{ old('is_active', true) ? 'checked' : '' }}/>
                                    <label for="is_active" class="block overflow-hidden h-6 rounded-full bg-slate-300 cursor-pointer peer-checked:bg-[#84994F]/50"></label>
                                </div>
                                <label for="is_active" class="text-sm font-bold text-slate-700 cursor-pointer">Aktifkan Reminder Ini</label>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: PESAN --}}
                        <div>
                            <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Konten Pesan</h6>

                            {{-- Pilih Template --}}
                            <div class="mb-5">
                                <label for="template_id" class="block text-sm font-bold text-slate-700 mb-1.5">Pilih Template (Opsional)</label>
                                <select id="template_id" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#FCB53B] focus:ring-1 focus:ring-[#FCB53B] transition cursor-pointer">
                                    <option value="">-- Pilih Template --</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->content }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Isi Pesan --}}
                            <div class="mb-5">
                                <label for="message_template" class="block text-sm font-bold text-slate-700 mb-2">Template Pesan <span class="text-red-500">*</span></label>
                                
                                {{-- Variable Helper --}}
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-mono rounded cursor-pointer hover:bg-slate-200" onclick="insertVar('{customer_name}')">{customer_name}</span>
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-mono rounded cursor-pointer hover:bg-slate-200" onclick="insertVar('{product_name}')">{product_name}</span>
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-mono rounded cursor-pointer hover:bg-slate-200" onclick="insertVar('{days_since}')">{days_since}</span>
                                </div>

                                <textarea id="message_template" name="message_template" rows="8" required
                                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-[#FCB53B] focus:ring-1 focus:ring-[#FCB53B] transition resize-none leading-relaxed font-mono text-slate-700">{{ old('message_template', "Halo {customer_name}! 👋\n\nSudah {days_since} hari sejak paket sampai. Stok masih aman?\n\nYuk order lagi sekarang!") }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Spacer Bawah --}}
                    <div class="h-16"></div>

                </form>
            </div>
        </div>

        {{-- Footer Actions (Fixed) --}}
        <div class="flex-none flex items-center justify-end gap-3 p-4 bg-white border-t border-slate-100 z-20">
            <a href="{{ route('reminders.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg font-bold text-sm hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" form="reminderForm" class="px-6 py-2.5 bg-[#FCB53B] text-white rounded-lg font-bold text-sm hover:bg-[#e09d22] shadow-md shadow-orange-100 transition flex items-center gap-2">
                <i data-lucide="save" class="h-4 w-4"></i> Simpan Rule
            </button>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();

            // Template Select Logic
            const templateSelect = document.getElementById('template_id');
            const messageInput = document.getElementById('message_template');
            
            if(templateSelect) {
                templateSelect.addEventListener('change', function() {
                    const content = this.value;
                    if (content) {
                        messageInput.value = content;
                    }
                });
            }
        });

        function insertVar(text) {
            const textarea = document.getElementById('message_template');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const before = textarea.value.substring(0, start);
            const after = textarea.value.substring(end, textarea.value.length);
            textarea.value = before + text + after;
            textarea.focus();
            textarea.selectionStart = textarea.selectionEnd = start + text.length;
        }
    </script>
    @endpush

@endsection