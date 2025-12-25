@extends('layouts.app')

@section('title', 'Otomasi Pesan - ROMS')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-gray-900">Dashboard Otomasi Pesan</h2>
    <a href="/otomasi-pesan/tambah" class="px-4 py-2 bg-[#FCB53B] text-white font-semibold rounded-lg hover:bg-[#e0a436] transition-colors flex items-center gap-2">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Tambah Otomasi Pesan</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Nama Pelanggan</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Produk</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Tanggal Pesan Dikirim</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Status Pesan</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-900">Ahmad Subagja</td>
                    <td class="px-6 py-3 text-gray-600">Kemeja Lengan Panjang</td>
                    <td class="px-6 py-3 text-gray-600">10 Nov 2025, 10:30</td>
                    <td class="px-6 py-3">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-[#84994F] text-white">Terkirim</span>
                    </td>
                </tr>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-900">Siti Lestari</td>
                    <td class="px-6 py-3 text-gray-600">Gamis Wanita Mocca</td>
                    <td class="px-6 py-3 text-gray-600">11 Nov 2025, 11:00</td>
                    <td class="px-6 py-3">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-[#FCB53B] text-gray-900">Tertunda</span>
                    </td>
                </tr>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-900">Budi Hartono</td>
                    <td class="px-6 py-3 text-gray-600">Celana Chino Pria</td>
                    <td class="px-6 py-3 text-gray-600">11 Nov 2025, 09:15</td>
                    <td class="px-6 py-3">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-[#B45253] text-white">Gagal</span>
                    </td>
                </tr>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-900">Dewi Anggraini</td>
                    <td class="px-6 py-3 text-gray-600">Sepatu Sneakers Putih</td>
                    <td class="px-6 py-3 text-gray-600">12 Nov 2025, 14:00</td>
                    <td class="px-6 py-3">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-[#FCB53B] text-gray-900">Tertunda</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection