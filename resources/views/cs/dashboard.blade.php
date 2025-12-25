@extends('layouts.app')

@section('title', 'Beranda CS')

@section('content')
    {{-- PAGE HEADER --}}
    <div class="flex-none mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#84994F] shadow-sm border border-slate-100">
                <i data-lucide="layout-dashboard" class="h-3 w-3"></i><span>Dashboard CS</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">
                Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
            </h1>
            <p class="mt-1 text-slate-500">Siap melayani pelanggan hari ini?</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Status Toggle --}}
            <form action="{{ route('cs.status.toggle') }}" method="POST">
                @csrf
                <button class="flex items-center gap-3 rounded-xl bg-white px-4 py-2 shadow-sm border border-slate-100 hover:bg-slate-50 transition cursor-pointer">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ Auth::user()->is_online ? 'bg-[#84994F]' : 'bg-slate-400' }} opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 {{ Auth::user()->is_online ? 'bg-[#84994F]' : 'bg-slate-400' }}"></span>
                    </span>
                    <div>
                        <div class="text-[10px] font-bold uppercase text-slate-400">Status</div>
                        <div class="text-xs font-bold {{ Auth::user()->is_online ? 'text-[#84994F]' : 'text-slate-500' }}">
                            {{ Auth::user()->is_online ? 'ONLINE' : 'OFFLINE' }}
                        </div>
                    </div>
                </button>
            </form>

            <div class="flex items-center gap-3 rounded-xl bg-white px-4 py-2 shadow-sm border border-slate-100">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#f1f5f9] text-[#84994F]">
                    <i data-lucide="calendar" class="h-4 w-4"></i>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase text-slate-400">Hari ini</div>
                    <div class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="flex-none grid gap-4 md:grid-cols-3 mb-6">
        <!-- Card 1: Unread Chats -->
        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm border-l-4 border-[#B45253] group hover:shadow-md transition-all">
             <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-slate-500 uppercase">Chat Belum Dibaca</p>
                <div class="text-[#B45253] bg-[#B45253]/10 p-2 rounded-lg"><i data-lucide="message-circle" class="h-5 w-5"></i></div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">
                {{ $unreadChatsCount }}
            </h3>
            <p class="text-[10px] text-slate-400 font-bold mt-1 flex items-center gap-1">
                Dari {{ $todayChatsCount }} chat hari ini
            </p>
        </div>

        <!-- Card 2: Pending Orders -->
        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm border-l-4 border-[#FCB53B] group hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-slate-500 uppercase">Pesanan Pending</p>
                <div class="text-[#FCB53B] bg-[#FCB53B]/10 p-2 rounded-lg"><i data-lucide="shopping-bag" class="h-5 w-5"></i></div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">
                {{ $pendingOrdersCount }}
            </h3>
            <p class="text-[10px] text-[#FCB53B] font-bold mt-1 flex items-center gap-1">
                Perlu diproses segera
            </p>
        </div>

        <!-- Card 3: Active Customers -->
        <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm border-l-4 border-[#84994F] group hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-slate-500 uppercase">Pelanggan Aktif</p>
                <div class="text-[#84994F] bg-[#84994F]/10 p-2 rounded-lg"><i data-lucide="users" class="h-5 w-5"></i></div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">
                {{ $activeCustomersCount }}
            </h3>
            <p class="text-[10px] text-slate-400 font-bold mt-1">
                Pelanggan yang Anda tangani
            </p>
        </div>
    </div>

    {{-- RECENT CHATS TABLE --}}
    <div class="rounded-2xl bg-white shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        <div class="p-5 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="message-square" class="h-4 w-4 text-[#84994F]"></i> Obrolan Terbaru
            </h3>
            <a href="{{ route('chat.ui') }}" class="text-xs font-bold text-[#84994F] hover:underline">Lihat Semua</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-[10px] uppercase text-slate-400 font-semibold">
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Pesan Terakhir</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-50">
                    @forelse($recentChats as $chat)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-700">{{ $chat['customer_name'] }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $chat['customer_phone'] }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs truncate">
                                {{ $chat['last_message'] }}
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $chat['last_message_time']->diffForHumans() }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($chat['status'] === 'unread')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100">Belum Dibaca</span>
                                @elseif($chat['status'] === 'read')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Dibaca</span>
                                @elseif($chat['status'] === 'closed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-600 border border-green-100">Selesai</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">{{ ucfirst($chat['status']) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('chat.ui', ['room' => $chat['room_id'] ?? null]) }}" class="p-2 bg-white border border-slate-200 text-slate-400 hover:text-[#84994F] hover:border-[#84994F] rounded-lg transition shadow-sm inline-flex">
                                    <i data-lucide="message-circle" class="h-4 w-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="bg-slate-50 p-3 rounded-full">
                                        <i data-lucide="message-square-off" class="h-6 w-6 opacity-20 text-slate-500"></i>
                                    </div>
                                    <p class="text-xs">Belum ada obrolan terbaru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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