@extends('layouts.app')

@section('title', 'Tambah Acara - ROMS')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-gray-900">Tambah Acara Baru</h2>
    <a href="/daftar-acara" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali ke Daftar</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
    <form action="#" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <div>
                <label for="nama_acara" class="block text-sm font-semibold text-gray-700 mb-2">Nama Acara</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-[#FCB53B] focus:ring-2 focus:ring-[#FCB53B]/20" id="nama_acara" placeholder="cth: Promo Akhir Tahun" required>
            </div>

            <div>
                <label for="jenis_acara" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Acara</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-[#FCB53B] focus:ring-2 focus:ring-[#FCB53B]/20" id="jenis_acara" required>
                    <option value="" selected disabled>-- Pilih Jenis Acara --</option>
                    <option value="produk_baru">Produk Baru</option>
                    <option value="diskon">Diskon</option>
                    <option value="hari_besar">Ucapan Hari Besar</option>
                </select>
            </div>
            
            <div>
                <label for="target_audiens" class="block text-sm font-semibold text-gray-700 mb-2">Target Audiens</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-[#FCB53B] focus:ring-2 focus:ring-[#FCB53B]/20" id="target_audiens" required>
                    <option value="" selected disabled>-- Pilih Target Audiens --</option>
                    <option value="aktif">Pelanggan Aktif</option>
                    <option value="baru">Pelanggan Baru</option>
                    <option value="loyal">Pelanggan Loyal</option>
                </select>
            </div>

            <div>
                <label for="waktu_pemicu" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pesan Dikirim</label>
                <input type="datetime-local" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-[#FCB53B] focus:ring-2 focus:ring-[#FCB53B]/20" id="waktu_pemicu" required>
            </div>

            <div class="md:col-span-2">
                <label for="isi_pesan" class="block text-sm font-semibold text-gray-700 mb-2">Isi Pesan (Opsional)</label>
                <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-[#FCB53B] focus:ring-2 focus:ring-[#FCB53B]/20" id="isi_pesan" rows="6" placeholder="Tulis isi pesan Anda di sini..."></textarea>
            </div>

            <div class="md:col-span-2 flex justify-end gap-2 pt-4">
                <button type="submit" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Simpan (Draft)
                </button>
                <button type="submit" class="px-4 py-2 bg-[#B45253] text-white font-semibold rounded-lg hover:bg-[#9a4243] transition-colors">
                    Simpan dan Aktifkan
                </button>
            </div>

        </div>
    </form>
</div>

@endsection