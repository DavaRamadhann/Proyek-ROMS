@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="file-text" class="h-6 w-6 text-[#84994F]"></i> Detail Pesanan
            </h1>
            <p class="text-sm text-slate-500 mt-1">Informasi lengkap pesanan <span class="font-mono font-bold text-slate-700">#{{ $order->order_number }}</span></p>
        </div>
        
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    {{-- CONTENT WRAPPER --}}
    <div class="flex justify-center pb-10">
        {{-- PERBAIKAN: Menggunakan w-full agar memenuhi layar --}}
        <div class="w-full">
            
            {{-- CARD UTAMA --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                
                {{-- Card Header (Hijau) --}}
                <div class="bg-[#84994F] px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-white">
                    <div>
                        <h5 class="text-lg font-bold flex items-center gap-2">
                            Order #{{ $order->order_number }}
                        </h5>
                        <p class="text-xs text-white/80 mt-1 flex items-center gap-1">
                            <i data-lucide="calendar" class="h-3 w-3"></i> 
                            Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div>
                        @if($order->status == 'shipped')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200">
                                <i data-lucide="truck" class="h-3 w-3"></i> SEDANG DIKIRIM
                            </span>
                        @elseif($order->status == 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold border border-green-200">
                                <i data-lucide="check-circle" class="h-3 w-3"></i> SELESAI
                            </span>
                        @elseif($order->status == 'cancelled')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold border border-red-200">
                                <i data-lucide="x-circle" class="h-3 w-3"></i> DIBATALKAN
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-bold border border-yellow-200">
                                <i data-lucide="clock" class="h-3 w-3"></i> {{ strtoupper($order->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-6 md:p-8">
                    
                    {{-- Grid Informasi (Pelanggan & Pengiriman) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 pb-8 border-b border-slate-100">
                        
                        {{-- Kolom Kiri: Info Pelanggan --}}
                        <div>
                            <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i data-lucide="user" class="h-4 w-4"></i> Informasi Pelanggan
                            </h6>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-[#84994F]/10 text-[#84994F] flex items-center justify-center">
                                    <i data-lucide="user" class="h-6 w-6"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-800 text-lg">{{ $order->customer->name }}</h5>
                                    <div class="space-y-1 mt-2">
                                        <p class="text-sm text-slate-500 flex items-center gap-2">
                                            <i data-lucide="phone" class="h-4 w-4 text-slate-400"></i> {{ $order->customer->phone }}
                                        </p>
                                        <p class="text-sm text-slate-500 flex items-start gap-2">
                                            <i data-lucide="map-pin" class="h-4 w-4 text-slate-400 mt-0.5 flex-shrink-0"></i> 
                                            {{ $order->customer->address ?? 'Alamat tidak tersedia' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Status Pengiriman & Update --}}
                        <div class="md:text-right flex flex-col md:items-end">
                            <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2 md:justify-end">
                                <i data-lucide="package-check" class="h-4 w-4"></i> Status Pengiriman
                            </h6>
                            
                            @if($order->shipped_at)
                                <div class="mb-4">
                                    <p class="font-bold text-green-600 flex items-center gap-2 md:justify-end">
                                        <i data-lucide="truck" class="h-4 w-4"></i> Pesanan Telah Dikirim
                                    </p>
                                    <small class="text-slate-400">Tanggal: {{ $order->shipped_at->format('d M Y') }}</small>
                                </div>
                            @else
                                <div class="mb-4 inline-flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 text-sm">
                                    <i data-lucide="info" class="h-4 w-4 text-blue-500"></i>
                                    Pesanan belum ditandai dikirim.
                                </div>
                            @endif

                            {{-- Form Update Status --}}
                            <div class="w-full max-w-xs">
                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                                    @csrf
                                    <div class="flex gap-2 mb-2">
                                        <select name="status" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-[#84994F] bg-white">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                        </select>
                                        <button type="submit" class="bg-[#B45253] hover:bg-[#9a4243] text-white text-xs font-bold px-3 py-2 rounded-lg transition whitespace-nowrap">
                                            Update
                                        </button>
                                    </div>

                                    {{-- Custom Message Collapsible --}}
                                    <details class="group text-left">
                                        <summary class="list-none text-xs text-slate-400 cursor-pointer hover:text-[#84994F] flex items-center gap-1 select-none">
                                            <i data-lucide="pencil-line" class="h-3 w-3"></i> Tulis pesan kustom?
                                        </summary>
                                        <div class="mt-2">
                                            <textarea name="custom_message" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 text-xs focus:outline-none focus:border-[#84994F]" rows="3" placeholder="Pesan notifikasi kustom (opsional)..."></textarea>
                                            <p class="text-[10px] text-slate-400 mt-1">Placeholder: [Name], [Order No], [Status]</p>
                                        </div>
                                    </details>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Rincian Item --}}
                    <h5 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i data-lucide="list" class="h-5 w-5 text-slate-400"></i> Rincian Item
                    </h5>
                    
                    <div class="overflow-x-auto rounded-lg border border-slate-100 mb-6">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Produk</th>
                                    <th class="px-4 py-3 text-center">Jumlah</th>
                                    <th class="px-4 py-3 text-right">Harga Satuan</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($order->items as $item)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $item->product_name }}</td>
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-slate-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50/80 border-t border-slate-100">
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-right font-bold text-slate-500 uppercase tracking-wider">Grand Total</td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="text-xl font-bold text-[#B45253]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Catatan Pesanan --}}
                    @if($order->notes)
                        <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-4 text-sm text-yellow-800 flex gap-3">
                            <i data-lucide="sticky-note" class="h-5 w-5 flex-shrink-0 mt-0.5 opacity-60"></i>
                            <div>
                                <h6 class="font-bold mb-1">Catatan Pesanan:</h6>
                                <p>{{ $order->notes }}</p>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Footer Card (Action Buttons) --}}
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end">
                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus pesanan ini permanen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 border border-red-200 text-red-600 rounded-lg text-sm font-bold hover:bg-red-50 hover:border-red-300 transition">
                            <i data-lucide="trash-2" class="h-4 w-4"></i> Hapus Pesanan
                        </button>
                    </form>
                </div>

            </div>
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