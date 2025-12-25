@extends('layouts.app')

@section('title', 'Broadcast WhatsApp')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="megaphone" class="h-6 w-6 text-[#84994F]"></i> Broadcast WhatsApp
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola kampanye pesan massal ke pelanggan Anda.</p>
        </div>
        
        <a href="{{ route('broadcast.create') }}" class="bg-[#B45253] hover:bg-[#9a4243] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-red-100 transition flex items-center gap-2">
            <i data-lucide="plus" class="h-4 w-4"></i> Buat Broadcast Baru
        </a>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-2 text-sm font-medium">
            <i data-lucide="check-circle-2" class="h-4 w-4"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        <div class="p-4 border-b border-slate-50 flex items-center gap-2 bg-slate-50/50">
            <i data-lucide="history" class="h-4 w-4 text-slate-400"></i>
            <h3 class="font-bold text-slate-700 text-sm">Riwayat Broadcast</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Judul Broadcast</th>
                        <th class="px-6 py-3">Target</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3">Jadwal</th>
                        <th class="px-6 py-3 text-center">Penerima</th>
                        <th class="px-6 py-3 text-center">Sukses/Gagal</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($broadcasts as $broadcast)
                    <tr class="hover:bg-slate-50 transition group">
                        
                        {{-- Judul --}}
                        <td class="px-6 py-4 font-bold text-slate-700">
                            {{ $broadcast->name }}
                        </td>

                        {{-- Target --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100">
                                {{ ucfirst($broadcast->target_segment) }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">
                            @if($broadcast->status == 'draft') 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold border border-slate-200">Draft</span>
                            @elseif($broadcast->status == 'scheduled') 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-[#FCB53B]/10 text-[#FCB53B] text-[10px] font-bold border border-[#FCB53B]/20">Terjadwal</span>
                            @elseif($broadcast->status == 'processing') 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-100 animate-pulse">Memproses</span>
                            @elseif($broadcast->status == 'completed') 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-[#84994F]/10 text-[#84994F] text-[10px] font-bold border border-[#84994F]/20">Selesai</span>
                            @endif
                        </td>

                        {{-- Jadwal --}}
                        <td class="px-6 py-4 text-slate-500 text-xs">
                            <div class="flex items-center gap-1.5">
                                <i data-lucide="calendar" class="h-3.5 w-3.5"></i>
                                {{ $broadcast->scheduled_at ? $broadcast->scheduled_at->format('d M Y H:i') : '-' }}
                            </div>
                        </td>

                        {{-- Penerima --}}
                        <td class="px-6 py-4 text-center text-slate-600">
                            {{ $broadcast->total_recipients }}
                        </td>

                        {{-- Sukses/Gagal --}}
                        <td class="px-6 py-4 text-center font-mono text-xs">
                            <span class="text-green-600 font-bold">{{ $broadcast->success_count }}</span>
                            <span class="text-slate-300 mx-1">/</span>
                            <span class="text-red-500 font-bold">{{ $broadcast->fail_count }}</span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('broadcast.show', $broadcast->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200 hover:border-blue-500 hover:text-blue-600 text-slate-500 rounded-lg transition text-xs font-bold shadow-sm">
                                    <i data-lucide="eye" class="h-3.5 w-3.5"></i> Detail
                                </a>
                                
                                <form action="{{ route('broadcast.destroy', $broadcast->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus broadcast \'{{ $broadcast->name }}\'? Semua log terkait juga akan dihapus. Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-red-200 hover:border-red-500 hover:bg-red-50 hover:text-red-600 text-red-500 rounded-lg transition text-xs font-bold shadow-sm">
                                        <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-3">
                                <div class="bg-slate-50 p-4 rounded-full">
                                    <i data-lucide="megaphone" class="h-8 w-8 opacity-20 text-slate-500"></i>
                                </div>
                                <p class="font-medium text-slate-600">Belum ada kampanye broadcast.</p>
                                <a href="{{ route('broadcast.create') }}" class="text-[#84994F] font-bold text-xs hover:underline flex items-center gap-1">
                                    <i data-lucide="plus" class="h-3 w-3"></i> Buat Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-slate-50 bg-slate-50/50">
             {{ $broadcasts->links() }}
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