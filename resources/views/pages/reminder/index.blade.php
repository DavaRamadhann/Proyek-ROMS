@extends('layouts.app')

@section('title', 'Jadwal Pengingat (Reminder)')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="bell-ring" class="h-6 w-6 text-[#84994F]"></i> Manajemen Reminder
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola aturan pengingat dan pantau jadwal pengiriman.</p>
        </div>
        
        <a href="{{ route('reminders.create') }}" class="bg-[#B45253] hover:bg-[#9a4243] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-red-100 transition flex items-center gap-2">
            <i data-lucide="plus" class="h-4 w-4"></i> Buat Rule Reminder
        </a>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-2 text-sm font-medium">
            <i data-lucide="check-circle-2" class="h-4 w-4"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE 1: ATURAN REMINDER --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col overflow-hidden mb-8">
        <div class="p-4 border-b border-slate-50 flex items-center gap-2 bg-slate-50/50">
            <i data-lucide="settings" class="h-4 w-4 text-[#FCB53B]"></i>
            <h3 class="font-bold text-slate-700 text-sm">Aturan Reminder Aktif</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Nama Rule</th>
                        <th class="px-6 py-3">Produk Pemicu</th>
                        <th class="px-6 py-3">Waktu Kirim</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($rules as $rule)
                    <tr class="hover:bg-slate-50 transition group">
                        <td class="px-6 py-4 font-bold text-slate-700">{{ $rule->name }}</td>
                        <td class="px-6 py-4">
                            @if($rule->product)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100">
                                    {{ $rule->product->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-500 text-xs font-bold border border-slate-200">Semua Produk</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $rule->days_after_delivery }} hari setelah sampai
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($rule->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('reminders.edit', $rule->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200 hover:border-[#FCB53B] hover:text-[#FCB53B] text-slate-500 rounded-lg transition text-xs font-bold shadow-sm">
                                <i data-lucide="pencil" class="h-3.5 w-3.5"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada aturan reminder.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABLE 2: LOG JADWAL --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        <div class="p-4 border-b border-slate-50 flex items-center gap-2 bg-slate-50/50">
            <i data-lucide="calendar-clock" class="h-4 w-4 text-slate-400"></i>
            <h3 class="font-bold text-slate-700 text-sm">Log Jadwal Pengiriman</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Order Terkait</th>
                        <th class="px-6 py-3">Jadwal Kirim</th>
                        <th class="px-6 py-3">Pesan Preview</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody" class="divide-y divide-slate-50 text-sm">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i data-lucide="loader-2" class="h-8 w-8 text-[#FCB53B] animate-spin"></i>
                                <span class="text-slate-500 font-medium text-sm">Menyinkronkan jadwal terbaru...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination Container (Filled via AJAX) --}}
        <div id="paginationContainer"></div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load logs via AJAX
            fetchLogs();
        });

        function fetchLogs() {
            const tbody = document.getElementById('logsTableBody');
            const pagination = document.getElementById('paginationContainer');
            
            fetch("{{ route('reminders.sync') }}")
                .then(response => response.text())
                .then(html => {
                    tbody.innerHTML = html;
                    if (window.lucide && window.lucide.createIcons && window.lucide.icons) {
                        window.lucide.createIcons({ icons: window.lucide.icons });
                    }
                })
                .catch(error => {
                    console.error('Error fetching logs:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-red-500">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="alert-circle" class="h-6 w-6"></i>
                                    <span>Gagal memuat data. Silakan muat ulang halaman.</span>
                                </div>
                            </td>
                        </tr>
                    `;
                    if (window.lucide && window.lucide.createIcons && window.lucide.icons) {
                        window.lucide.createIcons({ icons: window.lucide.icons });
                    }
                });
        }
    </script>
    @endpush

@endsection