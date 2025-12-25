@extends('layouts.app')

@section('title', 'Tambah CS - ROMS')

@section('content')

    {{-- HEADER PAGE (Fixed) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 flex-none">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="user-plus" class="h-6 w-6 text-[#FCB53B]"></i> Tambah CS Baru
            </h1>
            <p class="text-sm text-slate-500 mt-1">Daftarkan akun agen Customer Service baru.</p>
        </div>
        
        <a href="{{ route('admin.cs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    {{-- FORM WRAPPER (Scrollable) --}}
    <div class="w-full bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col h-[calc(100vh-160px)] overflow-hidden mb-10">
        
        {{-- Header Form --}}
        <div class="border-b border-slate-50 px-6 py-4 flex items-center gap-2 bg-slate-50/50 flex-none sticky top-0 z-10">
            <div class="p-2 bg-[#84994F]/10 rounded-lg text-[#84994F]">
                <i data-lucide="file-text" class="h-5 w-5"></i>
            </div>
            <h5 class="font-bold text-slate-700">Formulir Pendaftaran</h5>
        </div>

        {{-- Form Content --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar relative">
            <div class="p-6 md:p-8">
                
                {{-- Info Box --}}
                <div class="mb-6 p-4 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 flex items-start gap-3">
                    <i data-lucide="info" class="h-5 w-5 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm">
                        <strong>Info:</strong> CS dapat login langsung tanpa verifikasi email. Chat akan didistribusikan secara otomatis menggunakan sistem <em>round-robin</em>.
                    </div>
                </div>

                <form action="{{ route('admin.cs.store') }}" method="POST" id="createCsForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        {{-- KOLOM KIRI --}}
                        <div>
                            <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Identitas</h6>
                            
                            {{-- Nama --}}
                            <div class="mb-5">
                                <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Nama lengkap CS" required autofocus
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                                <p class="mt-1 text-xs text-slate-400">Nama akan ditampilkan di dashboard dan chat.</p>
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-5">
                                <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="mail" class="h-4 w-4 text-slate-400"></i>
                                    </div>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" required
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                                </div>
                                <p class="mt-1 text-xs text-slate-400">Digunakan untuk login ke sistem.</p>
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- KOLOM KANAN --}}
                        <div>
                            <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Keamanan</h6>

                            {{-- Password --}}
                            <div class="mb-5">
                                <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="lock" class="h-4 w-4 text-slate-400"></i>
                                    </div>
                                    <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                                </div>
                                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="mb-5">
                                <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="lock-keyhole" class="h-4 w-4 text-slate-400"></i>
                                    </div>
                                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" required
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Help Card (Gradient) --}}
                    <div class="mt-8 p-5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <i data-lucide="lightbulb" class="h-5 w-5 text-yellow-300"></i>
                            </div>
                            <div>
                                <h6 class="font-bold text-sm mb-2">Cara Kerja Sistem Round-Robin</h6>
                                <ul class="list-disc list-inside text-xs space-y-1 opacity-90">
                                    <li>Chat dari pelanggan didistribusikan secara merata ke semua CS.</li>
                                    <li>Customer A → CS 1, Customer B → CS 2, Customer C → CS 3, dst.</li>
                                    <li>Admin dapat melihat semua chat, CS hanya melihat chat mereka.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Spacer --}}
                    <div class="h-16"></div>

                </form>
            </div>
        </div>

        {{-- Footer Actions (Fixed) --}}
        <div class="flex-none flex items-center justify-end gap-3 p-4 bg-white border-t border-slate-100 z-20">
            <a href="{{ route('admin.cs.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg font-bold text-sm hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" form="createCsForm" class="px-6 py-2.5 bg-[#84994F] text-white rounded-lg font-bold text-sm hover:bg-[#6b7d3f] shadow-md shadow-green-100 transition flex items-center gap-2">
                <i data-lucide="check-circle" class="h-4 w-4"></i> Buat Akun CS
            </button>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
    @endpush

@endsection