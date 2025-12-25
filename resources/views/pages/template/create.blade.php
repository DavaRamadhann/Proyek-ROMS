@extends('layouts.app')

@section('title', 'Buat Template Baru')

@section('content')

    {{-- HEADER PAGE (Fixed) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 flex-none">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="plus-circle" class="h-6 w-6 text-[#84994F]"></i> Buat Template Baru
            </h1>
            <p class="text-sm text-slate-500 mt-1">Konfigurasi isi pesan template untuk broadcast atau reminder.</p>
        </div>
        
        <a href="{{ route('admin.templates.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    {{-- FORM WRAPPER (Scrollable & Full Width) --}}
    <div class="w-full bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col h-[calc(100vh-160px)] overflow-hidden">
        
        <div class="border-b border-slate-50 px-6 py-4 flex items-center gap-2 bg-slate-50/50 flex-none sticky top-0 z-10">
            <div class="p-2 bg-[#84994F]/10 rounded-lg text-[#84994F]">
                <i data-lucide="layout-template" class="h-5 w-5"></i>
            </div>
            <h5 class="font-bold text-slate-700">Formulir Template</h5>
        </div>

        {{-- Form Content (Scrollable) --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar relative">
            <div class="p-6 md:p-8">
                <form action="{{ route('admin.templates.store') }}" method="POST" id="templateForm">
                    @csrf

                    {{-- Nama Template --}}
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Template <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Promo Gajian, Reminder H-3" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                    </div>

                    {{-- Tipe --}}
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tipe Penggunaan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="type" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition cursor-pointer appearance-none">
                                <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>Umum</option>
                                <option value="broadcast" {{ old('type') == 'broadcast' ? 'selected' : '' }}>Broadcast Kampanye</option>
                                <option value="reminder" {{ old('type') == 'reminder' ? 'selected' : '' }}>Reminder Otomatis</option>
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-4 top-3 h-4 w-4 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Konten Pesan --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Konten Pesan <span class="text-red-500">*</span></label>
                        
                        {{-- Helper Variables & Formatting --}}
                        <div class="mb-3 flex flex-wrap gap-2 sticky top-0 bg-white pt-2 pb-2 z-10 border-b border-dashed border-slate-200 items-center">
                            <span class="text-xs text-slate-400 py-1.5 mr-1 flex items-center gap-1"><i data-lucide="code" class="h-3 w-3"></i> Variabel:</span>
                            <button type="button" onclick="insertVar('{customer_name}')" class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-md text-xs font-mono text-slate-600 hover:bg-[#84994F] hover:text-white hover:border-[#84994F] transition shadow-sm">
                                {customer_name}
                            </button>
                            <button type="button" onclick="insertVar('{order_number}')" class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-md text-xs font-mono text-slate-600 hover:bg-[#84994F] hover:text-white hover:border-[#84994F] transition shadow-sm">
                                {order_number}
                            </button>
                            <button type="button" onclick="insertVar('{product_name}')" class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-md text-xs font-mono text-slate-600 hover:bg-[#84994F] hover:text-white hover:border-[#84994F] transition shadow-sm">
                                {product_name}
                            </button>
                            <button type="button" onclick="insertVar('{days_since}')" class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-md text-xs font-mono text-slate-600 hover:bg-[#84994F] hover:text-white hover:border-[#84994F] transition shadow-sm">
                                {days_since}
                            </button>

                            <div class="w-px h-6 bg-slate-200 mx-1"></div>

                            <span class="text-xs text-slate-400 py-1.5 mr-1 flex items-center gap-1"><i data-lucide="type" class="h-3 w-3"></i> Format:</span>
                            <button type="button" onclick="insertVar('*Teks Tebal*')" class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-md text-xs font-bold text-slate-600 hover:bg-slate-100 transition shadow-sm">
                                Bold
                            </button>
                            <button type="button" onclick="insertVar('_Teks Miring_')" class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-md text-xs italic text-slate-600 hover:bg-slate-100 transition shadow-sm">
                                Italic
                            </button>
                        </div>

                        <textarea name="content" id="contentArea" rows="12" placeholder="Halo {customer_name}, terima kasih telah berbelanja..." required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition resize-none leading-relaxed font-mono text-slate-700 shadow-inner"
                            oninput="updatePreview()">{{ old('content') }}</textarea>
                        
                        {{-- Live Preview --}}
                        <div class="mt-4">
                            <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider flex items-center gap-1">
                                <i data-lucide="eye" class="h-3 w-3"></i> Preview Pesan WhatsApp
                            </p>
                            <div class="p-4 bg-[#E5DDD5] rounded-xl border border-slate-200 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#00A884] to-[#00A884]/80"></div>
                                <div class="bg-white p-3 rounded-lg shadow-sm text-sm text-slate-800 whitespace-pre-wrap leading-relaxed max-w-[90%] relative" id="message_preview">
                                    <span class="text-slate-400 italic">Pratinjau pesan akan muncul di sini...</span>
                                    <div class="text-[10px] text-slate-400 text-right mt-1 select-none">{{ date('H:i') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 flex items-start gap-2 text-xs text-slate-400 bg-blue-50 p-2 rounded-lg text-blue-600">
                            <i data-lucide="info" class="h-4 w-4 mt-0.5 flex-shrink-0"></i>
                            <span>Variabel dalam kurung kurawal <strong>{}</strong> akan otomatis diganti dengan data pelanggan saat pesan dikirim.</span>
                        </div>
                    </div>
                    
                    {{-- Spacer Bawah --}}
                    <div class="h-16"></div>

                </form>
            </div>
        </div>

        {{-- Footer Actions (Fixed) --}}
        <div class="flex-none flex items-center justify-end gap-3 p-4 bg-white border-t border-slate-100 z-20">
            <a href="{{ route('admin.templates.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg font-bold text-sm hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" form="templateForm" class="px-6 py-2.5 bg-[#84994F] text-white rounded-lg font-bold text-sm hover:bg-[#6b7d3f] shadow-md shadow-green-100 transition flex items-center gap-2">
                <i data-lucide="save" class="h-4 w-4"></i> Simpan Template
            </button>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initIcons();
            updatePreview(); // Init preview
        });

        function initIcons() {
            if (typeof lucide !== 'undefined') {
                if (lucide.icons) {
                    lucide.createIcons({ icons: lucide.icons });
                } else {
                    lucide.createIcons();
                }
            }
        }

        function insertVar(text) {
            const textarea = document.getElementById('contentArea');
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const textBefore = textarea.value.substring(0, start);
            const textAfter = textarea.value.substring(end, textarea.value.length);
            
            textarea.value = textBefore + text + textAfter;
            textarea.selectionStart = textarea.selectionEnd = start + text.length;
            textarea.focus();
            
            // Trigger input event to update preview
            textarea.dispatchEvent(new Event('input'));
        }

        function updatePreview() {
            const textarea = document.getElementById('contentArea');
            const preview = document.getElementById('message_preview');
            
            if (!textarea || !preview) return;

            const text = textarea.value;
            
            if (!text) {
                preview.innerHTML = '<span class="text-slate-400 italic">Pratinjau pesan akan muncul di sini...</span><div class="text-[10px] text-slate-400 text-right mt-1 select-none">{{ date("H:i") }}</div>';
                return;
            }

            // Format text for WhatsApp preview
            let formatted = text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;")
                .replace(/\n/g, '<br>')
                // Variables highlighting
                .replace(/\{([a-zA-Z0-9_]+)\}/g, '<span class="bg-yellow-100 text-yellow-800 px-1 rounded font-medium text-xs border border-yellow-200 shadow-sm">{$1}</span>')
                // WhatsApp Formatting
                .replace(/\*(.*?)\*/g, '<strong>$1</strong>')
                .replace(/_(.*?)_/g, '<em>$1</em>')
                .replace(/~(.*?)~/g, '<strike>$1</strike>')
                .replace(/```(.*?)```/gs, '<code class="bg-slate-100 px-1 rounded font-mono text-xs text-pink-600">$1</code>');

            preview.innerHTML = formatted + '<div class="text-[10px] text-slate-400 text-right mt-1 select-none flex justify-end items-center gap-0.5">{{ date("H:i") }} <i data-lucide="check-check" class="h-3 w-3 text-blue-500"></i></div>';
            
            initIcons();
        }
    </script>
    @endpush

@endsection