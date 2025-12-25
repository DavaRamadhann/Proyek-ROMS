@extends('layouts.app')

@section('title', 'Edit Pelanggan')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Pelanggan</h1>
            <p class="text-sm text-slate-500">Perbarui informasi detail dan lokasi pelanggan.</p>
        </div>
        <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:text-[#84994F] hover:border-[#84994F] transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
        </a>
    </div>

    {{-- MAIN FORM CARD --}}
    {{-- PERBAIKAN: Menghapus 'overflow-hidden' dan memastikan w-full agar tidak terpotong --}}
    <div class="w-full bg-white rounded-xl shadow-sm border border-slate-100">
        
        <div class="p-6 md:p-8">
            <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- SECTION 1: INFORMASI DASAR --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                        <i data-lucide="user" class="h-5 w-5 text-[#84994F]"></i> Informasi Dasar
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- WhatsApp (Read Only) --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Nomor WhatsApp</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="phone" class="h-4 w-4 text-slate-400"></i>
                                </div>
                                <input type="text" value="{{ $customer->phone ?? $customer->whatsapp }}" readonly disabled 
                                    class="w-full pl-10 pr-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-500 text-sm font-mono cursor-not-allowed">
                            </div>
                            <p class="mt-1 text-[10px] text-slate-400">Nomor WhatsApp adalah identitas utama dan tidak dapat diubah.</p>
                        </div>

                        {{-- Email --}}
                        <div class="col-span-1">
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" 
                                class="w-full px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Pelanggan --}}
                        <div class="col-span-1 md:col-span-2">
                            <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">
                                Nama Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                                class="w-full px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            <div class="mt-1.5 flex items-start gap-1.5 p-2 bg-blue-50 rounded-lg text-blue-600">
                                <i data-lucide="info" class="h-4 w-4 flex-shrink-0 mt-0.5"></i>
                                <p class="text-xs">Mengubah nama di sini akan mencegah sistem menimpanya dengan nama profil WhatsApp secara otomatis.</p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- SECTION 2: LOKASI & ALAMAT --}}
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                        <i data-lucide="map-pin" class="h-5 w-5 text-[#FCB53B]"></i> Lokasi Pengiriman
                    </h3>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        {{-- Kolom Kiri: Input Alamat --}}
                        <div class="lg:col-span-1">
                            <div class="mb-4">
                                <label for="address" class="block text-sm font-bold text-slate-700 mb-1.5">Alamat Lengkap</label>
                                <textarea id="address" name="address" rows="6" placeholder="Contoh: Jl. Mawar No. 12, RT 01/RW 02, Jakarta Selatan..."
                                    class="w-full px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition resize-none">{{ old('address', $customer->address) }}</textarea>
                                <button type="button" id="btn-search-address" class="mt-2 w-full px-4 py-2 bg-slate-100 border border-slate-200 text-slate-600 rounded-lg hover:border-[#84994F] hover:text-[#84994F] hover:bg-white transition flex items-center justify-center gap-2">
                                    <i data-lucide="search" class="h-4 w-4"></i>
                                    <span class="text-xs font-bold">Cari Alamat di Peta</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Latitude</label>
                                    <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $customer->latitude) }}" readonly 
                                        class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded text-xs font-mono text-slate-600">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Longitude</label>
                                    <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $customer->longitude) }}" readonly 
                                        class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded text-xs font-mono text-slate-600">
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Peta --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Pinpoint Lokasi</label>
                            <div class="relative w-full h-[400px] rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100">
                                <div id="map" class="w-full h-full z-0"></div>
                                
                                {{-- Tombol Deteksi Lokasi --}}
                                <button type="button" id="btn-detect-location" class="absolute top-3 right-3 z-[400] bg-white text-slate-700 p-2 rounded-lg shadow-md border border-slate-200 hover:bg-slate-50 hover:text-[#84994F] transition flex items-center gap-2 text-xs font-bold">
                                    <i data-lucide="crosshair" class="h-4 w-4"></i> Lokasi Saya
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-slate-400 flex items-center gap-1">
                                <i data-lucide="info" class="h-3 w-3"></i> Geser pin merah di peta untuk menyesuaikan titik koordinat yang akurat.
                            </p>
                        </div>

                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('customers.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg font-bold text-sm hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-[#84994F] text-white rounded-lg font-bold text-sm hover:bg-[#6b7d3f] shadow-md shadow-green-100 transition flex items-center gap-2">
                        <i data-lucide="save" class="h-4 w-4"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
    
    {{-- Spacer yang lebih besar agar scroll pasti bisa sampai bawah --}}
    <div class="h-32 w-full block"></div>

    {{-- LEAFLET CSS & JS --}}
    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- 1. SETUP MAP ---
            var defaultLat = -6.2088; // Jakarta
            var defaultLng = 106.8456;
            
            var savedLat = document.getElementById('latitude').value;
            var savedLng = document.getElementById('longitude').value;

            // Validasi agar tidak NaN
            var parsedLat = parseFloat(savedLat);
            var parsedLng = parseFloat(savedLng);

            var initialLat = !isNaN(parsedLat) ? parsedLat : defaultLat;
            var initialLng = !isNaN(parsedLng) ? parsedLng : defaultLng;
            var initialZoom = !isNaN(parsedLat) ? 16 : 11;

            // SCROLL FIX: scrollWheelZoom false agar tidak mengganggu scroll halaman
            var map = L.map('map', { 
                zoomControl: false,
                scrollWheelZoom: false 
            }).setView([initialLat, initialLng], initialZoom);
            
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var marker = L.marker([initialLat, initialLng], {
                draggable: true
            }).addTo(map);

            // --- 2. FUNGSI UPDATE INPUT ---
            function updateInputs(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(8);
                document.getElementById('longitude').value = lng.toFixed(8);
            }

            // --- 3. REVERSE GEOCODING ---
            async function reverseGeocode(lat, lng) {
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
                        headers: { 'User-Agent': 'ROMS-App/1.0' }
                    });
                    const data = await response.json();
                    
                    if (data && data.display_name) {
                        document.getElementById('address').value = data.display_name;
                    }
                } catch (error) {
                    console.error("Gagal reverse geocode:", error);
                }
            }

            // Event: Drag Marker
            marker.on('dragend', function(event) {
                var position = marker.getLatLng();
                updateInputs(position.lat, position.lng);
                reverseGeocode(position.lat, position.lng);
            });

            // Event: Klik Peta
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateInputs(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });

            // --- 4. FORWARD GEOCODING ---
            document.getElementById('btn-search-address').addEventListener('click', async function() {
                var address = document.getElementById('address').value;
                if (!address) return;

                var btn = this;
                var originalContent = btn.innerHTML;
                btn.innerHTML = '<span class="animate-spin h-4 w-4 border-2 border-slate-600 border-t-transparent rounded-full"></span> Mencari...';
                btn.disabled = true;

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`, {
                        headers: { 'User-Agent': 'ROMS-App/1.0' }
                    });
                    const data = await response.json();

                    if (data && data.length > 0) {
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);

                        map.setView([lat, lon], 16);
                        marker.setLatLng([lat, lon]);
                        updateInputs(lat, lon);
                    } else {
                        alert("Alamat tidak ditemukan di peta.");
                    }
                } catch (error) {
                    console.error("Gagal mencari alamat:", error);
                    alert("Terjadi kesalahan koneksi.");
                } finally {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                    lucide.createIcons();
                }
            });

            // --- 5. DETEKSI LOKASI SAYA ---
            document.getElementById('btn-detect-location').addEventListener('click', function() {
                if (navigator.geolocation) {
                    var btn = this;
                    var originalContent = btn.innerHTML;
                    btn.innerHTML = '<span class="animate-spin h-4 w-4 border-2 border-[#84994F] border-t-transparent rounded-full"></span> Mendeteksi...';
                    btn.disabled = true;

                    navigator.geolocation.getCurrentPosition(function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        updateInputs(lat, lng);
                        reverseGeocode(lat, lng);

                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                        lucide.createIcons();
                    }, function(error) {
                        alert("Gagal mendeteksi lokasi. Pastikan GPS aktif.");
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                        lucide.createIcons();
                    });
                } else {
                    alert("Browser Anda tidak mendukung Geolocation.");
                }
            });
        });
    </script>
    @endpush

@endsection