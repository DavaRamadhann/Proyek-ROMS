@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Pelanggan</h1>
            <p class="text-sm text-slate-500">Kelola database, segmen, dan riwayat belanja pelanggan.</p>
        </div>
        <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#84994F] hover:bg-[#6b7d3f] text-white rounded-lg text-sm font-bold shadow-md transition">
            <i data-lucide="user-plus" class="h-4 w-4"></i>
            Tambah Pelanggan Baru
        </a>
    </div>

    {{-- STATS SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4 hover:border-[#84994F] transition-colors">
            <div class="h-10 w-10 rounded-full bg-[#84994F]/10 text-[#84994F] flex items-center justify-center">
                <i data-lucide="users" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase">Total Pelanggan</p>
                <h3 class="text-lg font-bold text-slate-800">{{ $totalCustomers ?? 0 }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4 hover:border-[#FCB53B] transition-colors">
            <div class="h-10 w-10 rounded-full bg-[#FCB53B]/10 text-[#FCB53B] flex items-center justify-center">
                <i data-lucide="crown" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase">Big Spender</p>
                <h3 class="text-lg font-bold text-slate-800">{{ $bigSpenderCount ?? 0 }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4 hover:border-[#B45253] transition-colors">
            <div class="h-10 w-10 rounded-full bg-[#B45253]/10 text-[#B45253] flex items-center justify-center">
                <i data-lucide="user-x" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase">Inactive</p>
                <h3 class="text-lg font-bold text-slate-800">{{ $inactiveCount ?? 0 }}</h3>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT: TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        
        {{-- Toolbar: Search & Filter --}}
        <form method="GET" action="{{ route('customers.index') }}" class="p-4 border-b border-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center bg-slate-50/50">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, ID, atau WA..." class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#84994F] focus:border-[#84994F]">
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <select name="segment" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-600 focus:outline-none focus:border-[#84994F]">
                    <option value="">Semua Segmen</option>
                    <option value="Big Spender" {{ request('segment') == 'Big Spender' ? 'selected' : '' }}>Big Spender</option>
                    <option value="Loyal" {{ request('segment') == 'Loyal' ? 'selected' : '' }}>Loyal</option>
                    <option value="New Member" {{ request('segment') == 'New Member' ? 'selected' : '' }}>New Member</option>
                    <option value="Inactive" {{ request('segment') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Nama & ID</th>
                        <th class="px-6 py-3">Kontak (WA)</th>
                        <th class="px-6 py-3">Lokasi</th>
                        <th class="px-6 py-3 text-right">Total Belanja</th>
                        <th class="px-6 py-3 text-center">Segmen</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($customers as $customer)
                    
                    {{-- LOGIC: Menentukan Warna Segmen --}}
                    @php
                        $segmen = $customer->segment; 
                        
                        $badgeClass = '';
                        $iconSegmen = '';

                        switch($segmen) {
                            case 'Big Spender':
                                $badgeClass = 'bg-[#FCB53B]/10 text-[#FCB53B] border border-[#FCB53B]/20';
                                $iconSegmen = 'crown';
                                break;
                            case 'Loyal':
                                $badgeClass = 'bg-[#84994F]/10 text-[#84994F] border border-[#84994F]/20';
                                $iconSegmen = 'star';
                                break;
                            case 'Inactive':
                                $badgeClass = 'bg-slate-100 text-slate-500 border border-slate-200';
                                $iconSegmen = 'moon';
                                break;
                            case 'New Member':
                                $badgeClass = 'bg-blue-50 text-blue-600 border border-blue-100';
                                $iconSegmen = 'sparkles';
                                break;
                            default: // Regular
                                $badgeClass = 'bg-[#B45253]/10 text-[#B45253] border border-[#B45253]/20';
                                $iconSegmen = 'user';
                        }
                    @endphp

                    <tr class="hover:bg-slate-50 transition group">
                        {{-- Kolom 1: Nama & ID --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-[#84994F] font-bold border border-slate-200">
                                    {{ substr($customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-700 group-hover:text-[#84994F] transition">{{ $customer->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        ID: #CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom 2: Kontak --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-slate-600">
                                <i data-lucide="message-circle" class="h-3.5 w-3.5 text-green-500"></i> 
                                <span class="font-medium">{{ $customer->phone ?? $customer->whatsapp ?? '-' }}</span>
                            </div>
                        </td>

                        {{-- Kolom 3: Lokasi --}}
                        <td class="px-6 py-4 text-slate-500">
                            <div class="flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="h-3 w-3 text-slate-400"></i>
                                {{ $customer->city ?? 'Jakarta' }}
                            </div>
                        </td>

                        {{-- Kolom 4: Total Belanja --}}
                        <td class="px-6 py-4 text-right">
                            <div class="font-bold text-slate-700">
                                Rp {{ number_format($customer->total_spent ?? 0, 0, ',', '.') }} 
                            </div>
                            <div class="text-[10px] text-slate-400">
                                {{ $customer->orders_count ?? 0 }} Transaksi
                            </div>
                        </td>

                        {{-- Kolom 5: Segmen --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $badgeClass }}">
                                <i data-lucide="{{ $iconSegmen }}" class="h-3 w-3"></i>
                                {{ $segmen }}
                            </span>
                        </td>

                        {{-- Kolom 6: Aksi --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('customers.edit', $customer->id) }}" class="flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200 hover:border-[#FCB53B] hover:text-[#FCB53B] text-slate-500 rounded-lg transition text-xs font-semibold shadow-sm">
                                    <i data-lucide="pencil" class="h-3.5 w-3.5"></i> Edit
                                </a>
                                {{-- Tombol Delete (Opsional jika ingin disembunyikan hapus saja blok ini) --}}
                                <button onclick="confirmDelete('{{ route('customers.destroy', $customer->id) }}')" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Pelanggan">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-3">
                                <div class="bg-slate-50 p-4 rounded-full">
                                    <i data-lucide="users" class="h-8 w-8 opacity-20 text-slate-500"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-600">Belum ada data pelanggan.</p>
                                    <p class="text-xs text-slate-400">Mulai tambahkan pelanggan untuk melihat data disini.</p>
                                </div>
                                <a href="{{ route('customers.create') }}" class="mt-2 text-[#84994F] font-bold text-xs hover:underline flex items-center gap-1">
                                    <i data-lucide="plus" class="h-3 w-3"></i> Tambah Pelanggan Baru
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
             {{ $customers->links() }} 
        </div>
    </div>

    {{-- Script Tambahan --}}
    @push('scripts')
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete(url) {
            if(confirm('Apakah Anda yakin ingin menghapus data pelanggan ini? Data yang dihapus tidak dapat dikembalikan.')) {
                var form = document.getElementById('delete-form');
                form.action = url;
                form.submit();
            }
        }
    </script>
    @endpush

@endsection