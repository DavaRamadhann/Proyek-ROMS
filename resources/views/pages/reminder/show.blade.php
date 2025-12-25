@extends('layouts.app')

@section('title', 'Detail Reminder')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="bell" class="h-6 w-6 text-[#84994F]"></i> Detail Reminder
            </h1>
            <p class="text-sm text-slate-500 mt-1">Informasi lengkap dan statistik pengiriman reminder.</p>
        </div>
        
        <a href="{{ route('reminders.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    {{-- MAIN INFO CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        {{-- Header --}}
        <div class="px-6 py-4 bg-cyan-50 border-b border-cyan-200 flex items-center gap-2">
            <i data-lucide="info" class="h-5 w-5 text-cyan-600"></i>
            <h5 class="font-bold text-slate-800">Informasi Reminder</h5>
        </div>
        
        {{-- Body --}}
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Column 1 --}}
                <div>
                    <table class="w-full text-sm">
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 text-slate-600 font-bold w-48">Nama Reminder:</th>
                            <td class="py-3 text-slate-800 font-semibold">{{ $reminder->name }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 text-slate-600 font-bold">Produk:</th>
                            <td class="py-3">
                                @if($reminder->product)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                        {{ $reminder->product->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Semua Produk
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-left py-3 text-slate-600 font-bold">Hari Setelah Delivery:</th>
                            <td class="py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-100 text-cyan-700 border border-cyan-200">
                                    {{ $reminder->days_after_delivery }} Hari
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Column 2 --}}
                <div>
                    <table class="w-full text-sm">
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 text-slate-600 font-bold w-48">Status:</th>
                            <td class="py-3">
                                @if($reminder->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                        <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <i data-lucide="x-circle" class="h-3 w-3 mr-1"></i> Non-Aktif
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 text-slate-600 font-bold">Dibuat:</th>
                            <td class="py-3 text-slate-800">{{ $reminder->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="text-left py-3 text-slate-600 font-bold">Terakhir Update:</th>
                            <td class="py-3 text-slate-800">{{ $reminder->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Template Message Section --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h6 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <i data-lucide="message-square" class="h-5 w-5 text-[#84994F]"></i>
                    Template Pesan
                </h6>
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <pre class="text-sm text-slate-800 whitespace-pre-wrap font-sans">{{ $reminder->message_template }}</pre>
                </div>
                <div class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                    <p class="text-xs text-blue-700 font-semibold flex items-start gap-2">
                        <i data-lucide="info" class="h-4 w-4 flex-shrink-0 mt-0.5"></i>
                        <span>
                            <strong>Variable tersedia:</strong> 
                            <code class="bg-white px-1.5 py-0.5 rounded border border-blue-200 font-mono text-[10px] mx-1">{customer_name}</code>
                            <code class="bg-white px-1.5 py-0.5 rounded border border-blue-200 font-mono text-[10px] mx-1">{product_name}</code>
                            <code class="bg-white px-1.5 py-0.5 rounded border border-blue-200 font-mono text-[10px] mx-1">{order_date}</code>
                            <code class="bg-white px-1.5 py-0.5 rounded border border-blue-200 font-mono text-[10px] mx-1">{days_since}</code>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTICS CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 text-center hover:border-blue-300 transition">
            <h3 class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</h3>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-2">Total Log</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 text-center hover:border-green-300 transition">
            <h3 class="text-3xl font-bold text-green-600">{{ $stats['sent'] }}</h3>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-2">Terkirim</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 text-center hover:border-yellow-300 transition">
            <h3 class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</h3>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-2">Pending</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 text-center hover:border-red-300 transition">
            <h3 class="text-3xl font-bold text-red-600">{{ $stats['failed'] }}</h3>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-2">Gagal</p>
        </div>
    </div>

    {{-- LOG TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2">
            <i data-lucide="clock" class="h-5 w-5 text-slate-600"></i>
            <h6 class="font-bold text-slate-800">Riwayat Pengiriman</h6>
        </div>
        
        {{-- Table --}}
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Order ID</th>
                            <th class="px-4 py-3">Scheduled</th>
                            <th class="px-4 py-3">Sent</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Pesan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3">
                                <strong class="block text-slate-800">{{ $log->customer->name }}</strong>
                                <small class="text-slate-400">{{ $log->customer->phone }}</small>
                            </td>
                            <td class="px-4 py-3 text-slate-600 font-mono">#{{ $log->order_id }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $log->scheduled_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                @if($log->sent_at)
                                    <span class="text-slate-800">{{ $log->sent_at->format('d M Y H:i') }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->status === 'sent')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                        <i data-lucide="check" class="h-3 w-3 mr-1"></i> Terkirim
                                    </span>
                                @elseif($log->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                        <i data-lucide="clock" class="h-3 w-3 mr-1"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                        <i data-lucide="x" class="h-3 w-3 mr-1"></i> Gagal
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->message_sent)
                                    <button onclick="showMessage{{ $log->id }}()" 
                                        class="px-3 py-1.5 bg-cyan-50 text-cyan-600 border border-cyan-200 rounded-lg hover:bg-cyan-100 text-xs font-bold transition flex items-center gap-1">
                                        <i data-lucide="eye" class="h-3 w-3"></i> Lihat
                                    </button>

                                    {{-- Hidden Modal Content --}}
                                    <div id="messageModal{{ $log->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="hideMessage{{ $log->id }}()">
                                        <div class="bg-white rounded-xl max-w-lg w-full p-6" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-between mb-4">
                                                <h6 class="font-bold text-slate-800">Pesan yang Dikirim</h6>
                                                <button onclick="hideMessage{{ $log->id }}()" class="text-slate-400 hover:text-slate-600">
                                                    <i data-lucide="x" class="h-5 w-5"></i>
                                                </button>
                                            </div>
                                            <div class="bg-slate-50 p-4 rounded-lg">
                                                <pre class="whitespace-pre-wrap text-sm text-slate-700">{{ $log->message_sent }}</pre>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function showMessage{{ $log->id }}() {
                                            document.getElementById('messageModal{{ $log->id }}').classList.remove('hidden');
                                        }
                                        function hideMessage{{ $log->id }}() {
                                            document.getElementById('messageModal{{ $log->id }}').classList.add('hidden');
                                        }
                                    </script>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="bg-slate-50 p-4 rounded-full">
                                        <i data-lucide="inbox" class="h-8 w-8 opacity-20 text-slate-500"></i>
                                    </div>
                                    <p class="text-slate-400 font-medium">Belum ada log pengiriman</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
            <div class="mt-4 border-t border-slate-100 pt-4">
                {{ $logs->links() }}
            </div>
            @endif
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
