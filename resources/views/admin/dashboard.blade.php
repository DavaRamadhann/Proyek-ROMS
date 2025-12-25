@extends('layouts.app')

@section('title', 'Dashboard Admin - ROMS')

@section('content')

<h2 class="text-3xl font-bold text-gray-800 mb-6">Analisis</h2>

<div class="bg-[#84994F] text-white rounded-xl p-6 mb-8 shadow-lg flex flex-col md:flex-row justify-between items-center">
    <div class="flex items-center mb-4 md:mb-0">
        <div class="text-4xl mr-4 hidden md:block">💡</div>
        <div>
            <strong class="block text-lg">Halo Admin! Selamat Datang di ROMS.</strong>
            <small class="opacity-90">Berikut adalah ringkasan performa toko dan analisis pelanggan Anda.</small>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
    <div class="lg:col-span-12">
        <div class="flex justify-between items-end mb-4">
            <h4 class="text-xl font-bold text-gray-800 border-l-4 border-[#FCB53B] pl-3">Persebaran Area & Hotspot Pembelian</h4>
            <select class="form-select text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#84994F] focus:ring focus:ring-[#84994F]/20">
                <option>Kuartal Terakhir (Q3)</option>
                <option>Bulan Ini</option>
                <option>Tahun Ini</option>
            </select>
        </div>
    </div>

    <div class="lg:col-span-7">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden h-full relative">
            <div class="bg-gray-100 h-[350px] flex items-center justify-center text-gray-500 font-semibold relative">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Indonesia_location_map.svg/2400px-Indonesia_location_map.svg.png" 
                     alt="Peta Indonesia" 
                     class="absolute w-full h-full object-cover opacity-40">
                
                <div class="absolute top-[65%] left-[28%] w-6 h-6 bg-[#B45253]/80 rounded-full shadow-[0_0_0_5px_rgba(180,82,83,0.3)] cursor-help" title="Jakarta (High)"></div>
                <div class="absolute top-[68%] left-[32%] w-4 h-4 bg-[#FCB53B]/80 rounded-full cursor-help" title="Bandung (Medium)"></div>
                <div class="absolute top-[67%] left-[45%] w-[18px] h-[18px] bg-[#84994F]/80 rounded-full cursor-help" title="Surabaya (Medium)"></div>
                
                <div class="absolute bottom-5 right-5 bg-white p-3 rounded-lg text-xs shadow-md">
                    <div class="flex items-center mb-1"><span class="w-2.5 h-2.5 bg-[#B45253] rounded-full inline-block mr-2"></span> Tinggi</div>
                    <div class="flex items-center mb-1"><span class="w-2.5 h-2.5 bg-[#FCB53B] rounded-full inline-block mr-2"></span> Sedang</div>
                    <div class="flex items-center"><span class="w-2.5 h-2.5 bg-[#84994F] rounded-full inline-block mr-2"></span> Rendah</div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-5">
        <div class="bg-white rounded-xl shadow-sm h-full flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 font-bold text-gray-800">Top Kota Pembelian</div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Kota</th>
                            <th class="px-6 py-3 font-semibold">Vol. Order</th>
                            <th class="px-6 py-3 font-semibold text-right">Pertumbuhan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">Jakarta Selatan</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800">1,240</span>
                                    <div class="w-full h-2 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-[#B45253]" style="width: 90%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-green-600 font-medium"><i class="bi bi-arrow-up"></i> 12%</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">Bandung</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800">850</span>
                                    <div class="w-full h-2 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-[#FCB53B]" style="width: 65%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-green-600 font-medium"><i class="bi bi-arrow-up"></i> 8%</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">Surabaya</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800">420</span>
                                    <div class="w-full h-2 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-green-500" style="width: 40%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-red-500 font-medium"><i class="bi bi-arrow-down"></i> 2%</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">Medan</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800">310</span>
                                    <div class="w-full h-2 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-green-500" style="width: 30%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-green-600 font-medium"><i class="bi bi-arrow-up"></i> 5%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 text-center">
                <button class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Lihat Semua Lokasi</button>
            </div>
        </div>
    </div>
</div>

<div class="mb-8">
    <div class="flex justify-between items-end mb-4">
        <h4 class="text-xl font-bold text-gray-800 border-l-4 border-[#FCB53B] pl-3">Top Pelanggan & Segmen</h4>
        <div class="flex gap-2">
            <button class="px-3 py-1.5 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="bi bi-filter mr-1"></i> Filter
            </button>
            <button class="px-3 py-1.5 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="bi bi-download mr-1"></i> Export Laporan
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Pelanggan</th>
                        <th class="px-6 py-3 font-semibold">Segmen</th>
                        <th class="px-6 py-3 font-semibold text-center">Total Order (6 Bln)</th>
                        <th class="px-6 py-3 font-semibold">Total Belanja</th>
                        <th class="px-6 py-3 font-semibold">Terakhir Beli</th>
                        <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-800 flex items-center justify-center font-bold border border-gray-200 mr-3">AS</div>
                                <div>
                                    <div class="font-bold text-gray-800">Ahmad Subagja</div>
                                    <small class="text-gray-500">ahmad@dummy.com</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#84994F] text-white">Pelanggan Loyal</span></td>
                        <td class="px-6 py-4 font-bold text-center text-lg text-gray-800">24</td>
                        <td class="px-6 py-4 text-gray-800">Rp 5.400.000</td>
                        <td class="px-6 py-4 text-gray-500">2 hari lalu</td>
                        <td class="px-6 py-4 text-right">
                            <button class="px-3 py-1.5 text-xs text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Kirim Hadiah / Diskon Khusus">
                                <i class="bi bi-gift-fill mr-1"></i> Kirim Reward
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-800 flex items-center justify-center font-bold border border-gray-200 mr-3">SL</div>
                                <div>
                                    <div class="font-bold text-gray-800">Siti Lestari</div>
                                    <small class="text-gray-500">siti@dummy.com</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#45b6e8] text-white">Pelanggan Aktif</span></td>
                        <td class="px-6 py-4 font-bold text-center text-lg text-gray-800">10</td>
                        <td class="px-6 py-4 text-gray-800">Rp 1.850.000</td>
                        <td class="px-6 py-4 text-gray-500">1 minggu lalu</td>
                        <td class="px-6 py-4 text-right">
                            <button class="px-3 py-1.5 text-xs text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Kirim Hadiah / Diskon Khusus">
                                <i class="bi bi-gift-fill mr-1"></i> Kirim Reward
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-800 flex items-center justify-center font-bold border border-gray-200 mr-3">BH</div>
                                <div>
                                    <div class="font-bold text-gray-800">Budi Hartono</div>
                                    <small class="text-gray-500">budi@dummy.com</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#FCB53B] text-gray-800">Pelanggan Baru</span></td>
                        <td class="px-6 py-4 font-bold text-center text-lg text-gray-800">1</td>
                        <td class="px-6 py-4 text-gray-800">Rp 150.000</td>
                        <td class="px-6 py-4 text-gray-500">3 minggu lalu</td>
                        <td class="px-6 py-4 text-right">
                            <button class="px-3 py-1.5 text-xs text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Kirim Hadiah / Diskon Khusus">
                                <i class="bi bi-gift-fill mr-1"></i> Kirim Reward
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 text-center">
            <a href="#" class="text-sm font-bold text-[#84994F] hover:underline">Lihat Seluruh Data Pelanggan &rarr;</a>
        </div>
    </div>
</div>

@endsection