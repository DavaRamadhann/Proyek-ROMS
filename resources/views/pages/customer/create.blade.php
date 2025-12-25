@extends('layouts.app')

@section('title', 'Tambah Pelanggan')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="user-plus" class="h-6 w-6 text-[#84994F]"></i> Tambah Pelanggan Baru
            </h1>
            <p class="text-sm text-slate-500 mt-1">Tambahkan customer baru ke dalam database.</p>
        </div>
        
        <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    {{-- ALERT ERRORS --}}
    @if ($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start gap-3 text-sm">
            <i data-lucide="alert-circle" class="h-5 w-5 flex-shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <strong class="font-bold">Gagal menyimpan!</strong>
                <p class="text-xs mt-1">Ada beberapa kesalahan:</p>
                <ul class="list-disc pl-5 mt-1 text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- FORM CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        
        {{-- Card Header --}}
        <div class="border-b-4 border-[#84994F] bg-slate-50 px-6 py-4 flex items-center gap-3">
            <div class="p-2 bg-[#84994F]/10 rounded-lg text-[#84994F]">
                <i data-lucide="user-plus" class="h-5 w-5"></i>
            </div>
            <h5 class="font-bold text-slate-700">Form Data Pelanggan</h5>
        </div>

        {{-- Card Body --}}
        <div class="p-6 md:p-8">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf

                {{-- Section Title --}}
                <h6 class="text-xs uppercase text-slate-500 font-bold mb-4 tracking-wider">Informasi Dasar</h6>

                {{-- Nama --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:border-[#84994F] focus:outline-none focus:ring-2 focus:ring-[#84994F]/50 transition duration-200" 
                        id="name" name="name" value="{{ old('name', $prefillName ?? '') }}" 
                        placeholder="Nama lengkap pelanggan" required>
                    <div class="mt-1.5 text-xs text-slate-400 flex items-center gap-1">
                        <i data-lucide="info" class="h-3 w-3"></i>
                        Nama pelanggan akan dikunci (manual) setelah disimpan.
                    </div>
                </div>

                {{-- Grid Phone & Email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-bold text-slate-700 mb-1.5">
                            No. WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-green-500">
                                <i data-lucide="message-circle" class="h-4 w-4"></i>
                            </div>
                            <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:border-[#84994F] focus:outline-none focus:ring-2 focus:ring-[#84994F]/50 transition duration-200" 
                                id="phone" name="phone" value="{{ old('phone', $prefillPhone ?? '') }}" 
                                placeholder="08123456789" required>
                        </div>
                        <div class="mt-1.5 text-xs text-slate-400">Gunakan format 08... atau 62...</div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i data-lucide="mail" class="h-4 w-4"></i>
                            </div>
                            <input type="email" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:border-[#84994F] focus:outline-none focus:ring-2 focus:ring-[#84994F]/50 transition duration-200" 
                                id="email" name="email" value="{{ old('email') }}" 
                                placeholder="email@example.com">
                        </div>
                        <div class="mt-1.5 text-xs text-slate-400">Opsional.</div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="mb-6">
                    <label for="address" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Alamat Lengkap
                    </label>
                    <textarea class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:border-[#84994F] focus:outline-none focus:ring-2 focus:ring-[#84994F]/50 transition duration-200 resize-none" 
                        id="address" name="address" rows="3" 
                        placeholder="Alamat lengkap pelanggan...">{{ old('address') }}</textarea>
                    <div class="mt-1.5 text-xs text-slate-400">Alamat akan membantu dalam pengiriman pesanan.</div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex items-center justify-between pt-6 border-t border-slate-100">
                    <small class="text-xs text-slate-400 flex items-center gap-1">
                        <i data-lucide="info" class="h-3 w-3"></i> 
                        Data dengan tanda <span class="text-red-500">*</span> wajib diisi.
                    </small>
                    <div class="flex gap-3">
                        <a href="{{ route('customers.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-lg font-bold text-sm hover:bg-slate-200 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-[#84994F] text-white rounded-lg font-bold text-sm hover:bg-[#6b7d3f] shadow-md shadow-green-100 transition flex items-center gap-2">
                            <i data-lucide="save" class="h-4 w-4"></i> Simpan Pelanggan
                        </button>
                    </div>
                </div>

            </form>
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