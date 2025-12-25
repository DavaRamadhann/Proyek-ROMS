@extends('layouts.app')

@section('title', 'Manajemen Template Pesan')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="file-text" class="h-6 w-6 text-[#84994F]"></i> Template Pesan
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola format pesan untuk Broadcast dan Reminder.</p>
        </div>
        
        <a href="{{ route('admin.templates.create') }}" class="bg-[#84994F] hover:bg-[#6b7d3f] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-green-100 transition flex items-center gap-2">
            <i data-lucide="plus" class="h-4 w-4"></i> Buat Template
        </a>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-2 text-sm font-medium">
            <i data-lucide="check-circle-2" class="h-4 w-4"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nama Template</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Preview Konten</th>
                        <th class="px-6 py-4">Variabel</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($templates as $template)
                    <tr class="hover:bg-slate-50 transition">
                        
                        {{-- Nama --}}
                        <td class="px-6 py-4 font-bold text-slate-700">
                            {{ $template->name }}
                        </td>

                        {{-- Tipe --}}
                        <td class="px-6 py-4">
                            @if($template->type == 'broadcast')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100">
                                    Broadcast
                                </span>
                            @elseif($template->type == 'reminder')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-[#FCB53B]/10 text-[#FCB53B] text-xs font-bold border border-[#FCB53B]/20">
                                    Reminder
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-500 text-xs font-bold border border-slate-200">
                                    Umum
                                </span>
                            @endif
                        </td>

                        {{-- Preview --}}
                        <td class="px-6 py-4 text-slate-500">
                            <p class="line-clamp-2 max-w-xs" title="{{ $template->content }}">
                                {{ Str::limit($template->content, 60) }}
                            </p>
                        </td>

                        {{-- Variabel --}}
                        <td class="px-6 py-4">
                            @if($template->variables)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($template->variables as $var)
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-mono rounded border border-slate-200">
                                            {{ $var }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.templates.edit', $template->id) }}" class="p-2 bg-white border border-slate-200 text-slate-400 hover:text-[#FCB53B] hover:border-[#FCB53B] rounded-lg transition shadow-sm" title="Edit">
                                    <i data-lucide="pencil" class="h-4 w-4"></i>
                                </a>
                                
                                <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus template ini?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-white border border-slate-200 text-slate-400 hover:text-[#B45253] hover:border-[#B45253] rounded-lg transition shadow-sm" title="Hapus">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-3">
                                <div class="bg-slate-50 p-4 rounded-full">
                                    <i data-lucide="message-square" class="h-8 w-8 opacity-20 text-slate-500"></i>
                                </div>
                                <p class="font-medium text-slate-600">Belum ada template pesan.</p>
                                <a href="{{ route('admin.templates.create') }}" class="text-[#84994F] font-bold text-xs hover:underline flex items-center gap-1">
                                    <i data-lucide="plus" class="h-3 w-3"></i> Buat Baru
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
             {{ $templates->links() }}
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