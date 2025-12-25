@extends('layouts.app')

@section('title', 'Buat Broadcast Baru')

@section('content')

    {{-- HEADER PAGE (Fixed) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 flex-none">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="plus-circle" class="h-6 w-6 text-[#84994F]"></i> Buat Broadcast Baru
            </h1>
            <p class="text-sm text-slate-500 mt-1">Konfigurasi pesan dan target audiens.</p>
        </div>
        
        <a href="{{ route('broadcast.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    {{-- FORM WRAPPER (Scrollable) --}}
    <div class="w-full bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col h-[calc(100vh-160px)] overflow-hidden">
        
        {{-- Header Form --}}
        <div class="border-b border-slate-50 px-6 py-4 flex items-center gap-2 bg-slate-50/50 flex-none sticky top-0 z-10">
            <div class="p-2 bg-[#84994F]/10 rounded-lg text-[#84994F]">
                <i data-lucide="edit-3" class="h-5 w-5"></i>
            </div>
            <h5 class="font-bold text-slate-700">Form Broadcast</h5>
        </div>

        {{-- Form Content --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar relative">
            <div class="p-6 md:p-8">
                
                {{-- ALERT ERROR --}}
                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-2 text-sm font-medium">
                        <i data-lucide="alert-circle" class="h-4 w-4"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('broadcast.store') }}" method="POST" enctype="multipart/form-data" id="broadcastForm">
                    @csrf
                    
                    {{-- Judul --}}
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">Judul Broadcast</label>
                        <input type="text" id="name" name="name" required placeholder="Contoh: Promo Lebaran 2024"
                            class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                    </div>

                    {{-- Target Segmen --}}
                    <div class="mb-5">
                        <label for="target_segment" class="block text-sm font-bold text-slate-700 mb-1.5">Target Segmen</label>
                        <select id="target_segment" name="target_segment" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition cursor-pointer">
                            <option value="all">Semua Pelanggan</option>
                            <option value="loyal">Pelanggan Loyal (> 2 Pesanan)</option>
                            <option value="inactive">Pelanggan Tidak Aktif (No Order > 90 Hari)</option>
                            <option value="new">Pelanggan Baru (Terdaftar < 30 Hari)</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Pilih kelompok pelanggan yang akan menerima pesan ini.</p>
                    </div>

                    {{-- Pilih Template --}}
                    <div class="mb-5">
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="template_id" class="block text-sm font-bold text-slate-700">Pilih Template (Opsional)</label>
                            <a href="{{ route('admin.templates.index') }}" target="_blank" class="text-xs font-bold text-[#84994F] hover:underline flex items-center gap-1">
                                <i data-lucide="settings" class="h-3 w-3"></i> Kelola Template
                            </a>
                        </div>
                        <select id="template_id" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition cursor-pointer">
                            <option value="">-- Pilih Template --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Isi Pesan --}}
                    <div class="mb-5">
                        <label for="message_content" class="block text-sm font-bold text-slate-700 mb-1.5">Isi Pesan</label>
                        
                        {{-- Variable Helper Buttons --}}
                        <div class="flex flex-wrap gap-2 mb-2">
                            <button type="button" onclick="insertVariable('{customer_name}')" 
                                class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 text-xs font-medium rounded-md transition flex items-center gap-1.5">
                                <i data-lucide="user" class="h-3 w-3 text-[#84994F]"></i> 
                                Nama Pelanggan
                            </button>
                            <button type="button" onclick="insertVariable('*Teks Tebal*')" 
                                class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 text-xs font-bold rounded-md transition flex items-center gap-1.5">
                                <i data-lucide="bold" class="h-3 w-3"></i> 
                                Bold
                            </button>
                            <button type="button" onclick="insertVariable('_Teks Miring_')" 
                                class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 text-xs italic rounded-md transition flex items-center gap-1.5">
                                <i data-lucide="italic" class="h-3 w-3"></i> 
                                Italic
                            </button>
                        </div>

                        <textarea id="message_content" name="message_content" rows="8" required placeholder="Halo {customer_name}, kami ada promo spesial..."
                            class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition resize-none leading-relaxed font-mono"
                            oninput="updatePreview()"></textarea>
                        
                        {{-- Live Preview --}}
                        <div class="mt-4">
                            <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider flex items-center gap-1">
                                <i data-lucide="eye" class="h-3 w-3"></i> Preview Pesan WhatsApp
                            </p>
                            <div class="p-4 bg-[#E5DDD5] rounded-xl border border-slate-200 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#00A884] to-[#00A884]/80"></div>
                                <div class="bg-white p-3 rounded-lg shadow-sm text-sm text-slate-800 whitespace-pre-wrap leading-relaxed max-w-[90%] relative" id="message_preview">
                                    <span class="text-slate-400 italic">Pratinjau pesan akan muncul di sini...</span>
                                    
                                    {{-- Fake Time --}}
                                    <div class="text-[10px] text-slate-400 text-right mt-1 select-none">
                                        {{ date('H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Lampiran --}}
                    <div class="mb-5">
                        <label for="attachment" class="block text-sm font-bold text-slate-700 mb-1.5">Lampiran (Opsional)</label>
                        <input type="file" id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx"
                            class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-lg file:border-0
                            file:text-sm file:font-semibold
                            file:bg-[#84994F]/10 file:text-[#84994F]
                            hover:file:bg-[#84994F]/20
                            border border-slate-200 rounded-lg cursor-pointer">
                        <p class="mt-1 text-xs text-slate-400">Bisa berupa gambar (JPG/PNG) atau dokumen (PDF).</p>
                    </div>

                    {{-- Jadwal --}}
                    <div class="mb-8">
                        <label for="scheduled_at" class="block text-sm font-bold text-slate-700 mb-1.5">Jadwalkan Pengiriman (Opsional)</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                            class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                        <p class="mt-1 text-xs text-slate-400">Kosongkan jika ingin mengirim <strong>SEKARANG</strong> juga.</p>
                    </div>
                    
                    {{-- Spacer Bawah --}}
                    <div class="h-16"></div>

                </form>
            </div>
        </div>

        {{-- Footer Actions (Fixed) --}}
        <div class="flex-none flex items-center justify-end gap-3 p-4 bg-white border-t border-slate-100 z-20">
            <a href="{{ route('broadcast.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg font-bold text-sm hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" form="broadcastForm" class="px-6 py-2.5 bg-[#84994F] text-white rounded-lg font-bold text-sm hover:bg-[#6b7d3f] shadow-md shadow-green-100 transition flex items-center gap-2">
                <i data-lucide="send" class="h-4 w-4"></i> Simpan & Kirim
            </button>
        </div>

    </div>

    @push('scripts')
    <script>
        // Pass templates data to JS
        const templates = @json($templates);

        // Global function for variable insertion
        function insertVariable(variable) {
            const textarea = document.getElementById('message_content');
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const before = text.substring(0, start);
            const after = text.substring(end, text.length);
            
            textarea.value = before + variable + after;
            textarea.selectionStart = textarea.selectionEnd = start + variable.length;
            textarea.focus();
            
            // Trigger input event to update preview
            textarea.dispatchEvent(new Event('input'));
        }

        // Global function for preview update
        function updatePreview() {
            const messageInput = document.getElementById('message_content');
            const preview = document.getElementById('message_preview');
            
            if (!messageInput || !preview) return;

            const text = messageInput.value;
            
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
                .replace(/{customer_name}/g, '<span class="bg-yellow-100 text-yellow-800 px-1 rounded font-medium text-xs border border-yellow-200 shadow-sm">{Nama Pelanggan}</span>')
                .replace(/{name}/g, '<span class="bg-yellow-100 text-yellow-800 px-1 rounded font-medium text-xs border border-yellow-200 shadow-sm">{Nama Pelanggan}</span>')
                // WhatsApp Formatting
                .replace(/\*(.*?)\*/g, '<strong>$1</strong>')
                .replace(/_(.*?)_/g, '<em>$1</em>')
                .replace(/~(.*?)~/g, '<strike>$1</strike>')
                .replace(/```(.*?)```/gs, '<code class="bg-slate-100 px-1 rounded font-mono text-xs text-pink-600">$1</code>');

            preview.innerHTML = formatted + '<div class="text-[10px] text-slate-400 text-right mt-1 select-none flex justify-end items-center gap-0.5">{{ date("H:i") }} <i data-lucide="check-check" class="h-3 w-3 text-blue-500"></i></div>';
            
            // Re-initialize icons in preview if any
            initIcons();
        }

        function initIcons() {
            if (typeof lucide !== 'undefined') {
                if (lucide.icons) {
                    lucide.createIcons({ icons: lucide.icons });
                } else {
                    lucide.createIcons();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initIcons();

            const templateSelect = document.getElementById('template_id');
            const messageInput = document.getElementById('message_content');
            
            // Template Selection Handler
            if(templateSelect) {
                templateSelect.addEventListener('change', function() {
                    const selectedId = this.value;
                    if (selectedId) {
                        // Find template by ID
                        const template = templates.find(t => t.id == selectedId);
                        if (template) {
                            messageInput.value = template.content;
                            messageInput.dispatchEvent(new Event('input')); // Update preview
                        }
                    }
                });
            }
        });
    </script>
    @endpush
@endsection