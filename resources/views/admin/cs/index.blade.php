@extends('layouts.app')

@section('title', 'Kelola CS - ROMS')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="users" class="h-6 w-6 text-[#84994F]"></i> Manajemen CS
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola akun, status, dan penugasan chat agen.</p>
        </div>
        
        <a href="{{ route('admin.cs.create') }}" class="bg-[#B45253] hover:bg-[#9a4243] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-red-100 transition flex items-center gap-2">
            <i data-lucide="user-plus" class="h-4 w-4"></i> Tambah CS Baru
        </a>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        
        <!-- Card 1: Total CS -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="users" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Total CS</p>
                <h3 class="text-xl font-bold text-slate-800">{{ $totalCS }}</h3>
            </div>
        </div>

        <!-- Card 2: CS Online -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                <i data-lucide="circle-dot" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">CS Online</p>
                <h3 class="text-xl font-bold text-slate-800">{{ $activeCS }}</h3>
            </div>
        </div>

        <!-- Card 3: Chat Assigned -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center">
                <i data-lucide="message-circle" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Total Chat Assigned</p>
                <h3 class="text-xl font-bold text-slate-800">{{ $totalChatsAssigned }}</h3>
            </div>
        </div>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-2 text-sm font-medium">
            <i data-lucide="check-circle-2" class="h-4 w-4"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        <div class="p-4 border-b border-slate-50 flex items-center gap-2 bg-slate-50/50">
            <i data-lucide="list" class="h-4 w-4 text-slate-400"></i>
            <h3 class="font-bold text-slate-700 text-sm">Daftar Customer Service</h3>
        </div>

        @if($csUsers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Nama & Email</th>
                        <th class="px-6 py-3 text-center">Chat Assigned</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3">Terdaftar</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @foreach($csUsers as $index => $cs)
                    <tr class="hover:bg-slate-50 transition group">
                        <td class="px-6 py-4 text-slate-500">{{ $csUsers->firstItem() + $index }}</td>
                        
                        {{-- Nama --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-[#84994F]/10 text-[#84994F] flex items-center justify-center font-bold text-sm border border-[#84994F]/20">
                                    {{ strtoupper(substr($cs->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-700">{{ $cs->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $cs->email }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Chat Count --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">
                                {{ $cs->assigned_chats_count ?? 0 }} chats
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">
                            @if($cs->is_online)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Online
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Offline
                                </span>
                            @endif
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-6 py-4 text-slate-500 text-xs">
                            {{ $cs->created_at->format('d M Y') }}
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.cs.edit', $cs->id) }}" class="p-1.5 bg-white border border-slate-200 text-slate-500 rounded-lg hover:border-blue-500 hover:text-blue-600 transition shadow-sm">
                                    <i data-lucide="pencil" class="h-4 w-4"></i>
                                </a>
                                <form action="{{ route('admin.cs.destroy', $cs->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus CS ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-white border border-slate-200 text-slate-500 rounded-lg hover:border-red-500 hover:text-red-600 transition shadow-sm">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-50 bg-slate-50/50">
             {{ $csUsers->links() }}
        </div>

        @else
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="bg-slate-50 p-4 rounded-full mb-4">
                    <i data-lucide="user-x" class="h-10 w-10 text-slate-300"></i>
                </div>
                <p class="text-slate-500 font-medium mb-4">Belum ada CS yang terdaftar.</p>
                <a href="{{ route('admin.cs.create') }}" class="text-[#84994F] font-bold text-sm hover:underline flex items-center gap-1">
                    <i data-lucide="plus" class="h-4 w-4"></i> Tambah CS Pertama
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
    @endpush

@endsection