@extends('layouts.app')

@section('title', 'Chat Inbox')

@section('content')

    {{-- WhatsApp Connection Alert --}}
    @if(isset($isConnected) && !$isConnected)
    <div class="mb-6 p-4 rounded-xl bg-yellow-50 border border-yellow-200 flex items-start gap-3">
        <i data-lucide="alert-triangle" class="h-6 w-6 text-yellow-600 flex-shrink-0 mt-0.5"></i>
        <div class="flex-1">
            <h6 class="font-bold text-yellow-800 mb-1">WhatsApp Belum Terhubung</h6>
            <p class="text-sm text-yellow-700 mb-3">Untuk menggunakan fitur chat, Anda perlu menghubungkan akun WhatsApp terlebih dahulu.</p>
            <a href="{{ route('whatsapp.scan') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-bold text-sm transition shadow-md">
                <i data-lucide="qr-code" class="h-4 w-4"></i> Scan QR Code
            </a>
        </div>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6 rounded-2xl bg-gradient-to-br from-[#84994F] to-[#6b7d3f] p-6 shadow-lg text-white">
        <div>
            <h3 class="text-2xl font-bold flex items-center gap-2 mb-1">
                <i data-lucide="message-circle" class="h-6 w-6"></i> Chat Inbox
            </h3>
            <p class="text-white/80 text-sm">Daftar percakapan dengan pelanggan</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-5">
            <small class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Chat</small>
            <h4 class="text-3xl font-bold text-blue-600 mt-1">{{ $rooms->count() }}</h4>
        </div>
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-500 p-5">
            <small class="text-slate-500 text-xs font-bold uppercase tracking-wider">Chat Baru</small>
            <h4 class="text-3xl font-bold text-red-600 mt-1">{{ $rooms->where('status', 'new')->count() }}</h4>
        </div>
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-green-500 p-5">
            <small class="text-slate-500 text-xs font-bold uppercase tracking-wider">Open</small>
            <h4 class="text-3xl font-bold text-green-600 mt-1">{{ $rooms->where('status', 'open')->count() }}</h4>
        </div>
    </div>

    {{-- Chat List --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        @forelse ($rooms as $room)
            <a href="{{ route('chat.show', $room->id) }}" 
               class="block p-5 border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition-all hover:translate-x-1 group"
               id="chat-item-{{ $room->id }}">
                <div class="flex w-full justify-between items-start gap-4">
                    {{-- Left: Avatar & Info --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold text-lg">
                                {{ strtoupper(substr($room->customer->name, 0, 2)) }}
                            </div>
                            <div>
                                <h5 class="font-bold text-slate-800 group-hover:text-[#84994F] transition">{{ $room->customer->name }}</h5>
                                <small class="text-slate-400 text-xs flex items-center gap-1">
                                    <i data-lucide="phone" class="h-3 w-3"></i> {{ $room->customer->phone }}
                                </small>
                            </div>
                        </div>
                    </div>
                    {{-- Right: Time & Status --}}
                    <div class="text-right flex-shrink-0">
                        <small class="text-slate-400 text-xs block mb-2 flex items-center justify-end gap-1">
                            <i data-lucide="clock" class="h-3 w-3"></i> {{ $room->updated_at->diffForHumans() }}
                        </small>
                        @if ($room->status == 'new')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                <i data-lucide="bell" class="h-3 w-3"></i> BARU
                            </span>
                        @elseif ($room->status == 'open')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                <i data-lucide="message-square" class="h-3 w-3"></i> Open
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                {{ $room->status }}
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <div class="mb-4 inline-block p-4 bg-slate-50 rounded-full">
                    <i data-lucide="inbox" class="h-12 w-12 text-slate-300"></i>
                </div>
                <h5 class="text-slate-600 font-bold mb-1">Inbox Kosong</h5>
                <p class="text-slate-400 text-sm">Belum ada percakapan yang tersedia</p>
            </div>
        @endforelse
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