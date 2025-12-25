@extends('layouts.app')

@section('title', 'Detail Broadcast')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="info" class="h-6 w-6 text-[#84994F]"></i> Detail Broadcast
            </h1>
            <p class="text-sm text-slate-500 mt-1">Informasi lengkap kampanye broadcast.</p>
        </div>
        
        <a href="{{ route('broadcast.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        
        {{-- KOLOM KIRI: INFO BROADCAST --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden h-full">
                <div class="p-4 border-b border-slate-50 bg-slate-50/50 flex items-center gap-2">
                    <i data-lucide="file-text" class="h-4 w-4 text-slate-400"></i>
                    <h3 class="font-bold text-slate-700 text-sm">Informasi Broadcast</h3>
                </div>
                
                <div class="p-5 text-sm space-y-4">
                    <div>
                        <span class="block text-xs text-slate-400 uppercase font-bold tracking-wider mb-1">Judul</span>
                        <p class="font-bold text-slate-800">{{ $broadcast->name }}</p>
                    </div>

                    <div>
                        <span class="block text-xs text-slate-400 uppercase font-bold tracking-wider mb-1">Status</span>
                        @if($broadcast->status == 'draft') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold border border-slate-200">Draft</span>
                        @elseif($broadcast->status == 'scheduled') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-[#FCB53B]/10 text-[#FCB53B] text-xs font-bold border border-[#FCB53B]/20">Terjadwal</span>
                        @elseif($broadcast->status == 'processing') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100 animate-pulse">Memproses</span>
                        @elseif($broadcast->status == 'completed') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-[#84994F]/10 text-[#84994F] text-xs font-bold border border-[#84994F]/20">Selesai</span>
                        @endif
                    </div>

                    <div>
                        <span class="block text-xs text-slate-400 uppercase font-bold tracking-wider mb-1">Target</span>
                        <p class="text-slate-700">{{ ucfirst($broadcast->target_segment) }}</p>
                    </div>

                    <div>
                        <span class="block text-xs text-slate-400 uppercase font-bold tracking-wider mb-1">Jadwal</span>
                        <div class="flex items-center gap-2 text-slate-700">
                            <i data-lucide="clock" class="h-4 w-4 text-slate-400"></i>
                            {{ $broadcast->scheduled_at ? $broadcast->scheduled_at->format('d M Y H:i') : 'Langsung' }}
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs text-slate-400 uppercase font-bold tracking-wider mb-1">Dibuat Oleh</span>
                        <p class="text-slate-700">{{ $broadcast->creator->name ?? '-' }}</p>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <span class="block text-xs text-slate-400 uppercase font-bold tracking-wider mb-2">Pesan</span>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 text-sm whitespace-pre-wrap leading-relaxed">{{ $broadcast->message_content }}</div>
                    </div>

                    @if($broadcast->attachment_url)
                        <div class="border-t border-slate-100 pt-4">
                            <span class="block text-xs text-slate-400 uppercase font-bold tracking-wider mb-2">Lampiran</span>
                            @if($broadcast->attachment_type == 'image')
                                <img src="{{ $broadcast->attachment_url }}" class="w-full rounded-lg border border-slate-200">
                            @else
                                <a href="{{ $broadcast->attachment_url }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg border border-blue-100 hover:bg-blue-100 transition text-sm font-bold">
                                    <i data-lucide="paperclip" class="h-4 w-4"></i> Lihat Lampiran
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: LOG PENGIRIMAN --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-full">
                
                <div class="p-4 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <i data-lucide="list" class="h-4 w-4 text-slate-400"></i>
                        <h3 class="font-bold text-slate-700 text-sm">Log Pengiriman</h3>
                    </div>
                    <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2 py-1 rounded-full">
                        Total: {{ $broadcast->total_recipients }}
                    </span>
                </div>

                <div class="p-5 flex-1 flex flex-col">
                    
                    {{-- STATS LOG --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-green-50 rounded-xl border border-green-100 text-center">
                            <h3 class="text-2xl font-bold text-green-600">{{ $broadcast->success_count }}</h3>
                            <p class="text-xs font-bold text-green-800 uppercase tracking-wide">Berhasil</p>
                        </div>
                        <div class="p-4 bg-red-50 rounded-xl border border-red-100 text-center">
                            <h3 class="text-2xl font-bold text-red-600">{{ $broadcast->fail_count }}</h3>
                            <p class="text-xs font-bold text-red-800 uppercase tracking-wide">Gagal</p>
                        </div>
                    </div>

                    {{-- TABLE LOG --}}
                    <div class="flex-1 overflow-x-auto rounded-lg border border-slate-100">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Pelanggan</th>
                                    <th class="px-4 py-3">No. HP</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3">Waktu / Error</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($broadcast->logs as $log)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $log->customer->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $log->customer->phone ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($log->status == 'sent') 
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold">
                                                <i data-lucide="check" class="h-3 w-3"></i> Terkirim
                                            </span>
                                        @else 
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold">
                                                <i data-lucide="x" class="h-3 w-3"></i> Gagal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs">
                                        @if($log->status == 'sent')
                                            <span class="text-slate-500">{{ $log->sent_at ? $log->sent_at->format('H:i:s') : '-' }}</span>
                                        @else
                                            <span class="text-red-500 font-medium" title="{{ $log->error_message }}">
                                                {{ Str::limit($log->error_message, 40) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada log pengiriman.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
    @endpush

@endsection