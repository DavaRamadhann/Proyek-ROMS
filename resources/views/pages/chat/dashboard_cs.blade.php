@extends('layout.cs_main')

@section('title', 'Beranda CS - ROMS')

@section('main-content')

<h2 class="text-3xl font-bold text-gray-900 mb-6">Halo, Customer Service! 👋</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:-translate-y-1 transition-transform">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Obrolan Menunggu</span>
                <h3 class="text-4xl font-bold text-[#B45253] my-2">5</h3>
                <small class="text-gray-500">Perlu respon segera</small>
            </div>
            <div class="text-6xl text-[#B45253] opacity-25">
                <i class="bi bi-chat-dots-fill"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:-translate-y-1 transition-transform">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pesanan Baru</span>
                <h3 class="text-4xl font-bold text-[#FCB53B] my-2">12</h3>
                <small class="text-gray-500">Menunggu konfirmasi</small>
            </div>
            <div class="text-6xl text-[#FCB53B] opacity-25">
                <i class="bi bi-box-seam-fill"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:-translate-y-1 transition-transform">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan Aktif</span>
                <h3 class="text-4xl font-bold text-[#84994F] my-2">150</h3>
                <small class="text-gray-500">Total database pelanggan</small>
            </div>
            <div class="text-6xl text-[#84994F] opacity-25">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h5 class="font-bold text-gray-900">Aktivitas Terkini</h5>
        <a href="{{ route('chat.whatsapp') }}" class="px-3 py-1.5 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Lihat Semua Obrolan</a>
    </div>
    <div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Pelanggan</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Aktivitas</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Waktu</th>
                    <th class="text-right px-6 py-3 font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-3"><span class="font-semibold text-gray-900">Ahmad Subagja</span></td>
                    <td class="px-6 py-3 text-gray-600">Mengirim pesan baru</td>
                    <td class="px-6 py-3 text-gray-600">10:30 WIB</td>
                    <td class="px-6 py-3 text-right"><span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Belum Dibaca</span></td>
                </tr>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-3"><span class="font-semibold text-gray-900">Siti Lestari</span></td>
                    <td class="px-6 py-3 text-gray-600">Pesanan #ORDER-123 selesai</td>
                    <td class="px-6 py-3 text-gray-600">09:15 WIB</td>
                    <td class="px-6 py-3 text-right"><span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection