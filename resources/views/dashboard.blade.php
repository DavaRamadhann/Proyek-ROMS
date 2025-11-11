@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                Selamat Datang di Dashboard ROMS
                {{-- Form logout kita pindahkan ke layout.app.blade.php --}}
            </div>
            <div class="card-body">
                <p>Halo, <strong>{{ auth()->user()->name }}</strong>!</p>
                <p>Anda login sebagai: <strong>{{ strtoupper(auth()->user()->role) }}</strong></p>
            </div>
        </div>
    </div>
</div>

{{-- =============================================== --}}
{{-- ============ TAMBAHAN BARU DI SINI ============ --}}
{{-- =============================================== --}}

<div class="row">
    <div class="col-md-12">
        <h3>Modul Aplikasi</h3>
        <hr>
    </div>

    {{-- Kartu Pintas untuk Fitur CS --}}
    {{-- Hanya tampil jika role-nya 'admin' atau 'cs' --}}
    @if(in_array(auth()->user()->role, ['admin', 'cs']))
    <div class="col-md-4">
        <div class="card text-white bg-primary shadow">
            <div class="card-body">
                <h5 class="card-title">💬 Chat Customer Service</h5>
                <p class="card-text">Buka inbox untuk melihat dan membalas semua pesan pelanggan.</p>
                <a href="{{ route('chat.index') }}" class="btn btn-light stretched-link">Buka Inbox</a>
            </div>
        </div>
    </div>
    @endif

    {{-- Kartu Pintas untuk Fitur Customer (Nanti) --}}
    @if(in_array(auth()->user()->role, ['admin', 'cs']))
    <div class="col-md-4">
        <div class="card text-white bg-success shadow">
            <div class="card-body">
                <h5 class="card-title">👥 Manajemen Pelanggan</h5>
                <p class="card-text">Kelola data master pelanggan, lihat riwayat, dan atur segmen.</p>
                <a href="#" class="btn btn-light stretched-link disabled">(Segera)</a>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Kartu Pintas untuk Admin (Nanti) --}}
    @if(auth()->user()->role == 'admin')
    <div class="col-md-4">
        <div class="card text-white bg-dark shadow">
            <div class="card-body">
                <h5 class="card-title">⚙️ Pengaturan</h5>
                <p class="card-text">Kelola pengguna (CS), aturan pengingat, dan integrasi API.</p>
                <a href="#" class="btn btn-light stretched-link disabled">(Segera)</a>
            </div>
        </div>
    </div>
    @endif

</div>
{{-- =============================================== --}}
{{-- ============ AKHIR TAMBAHAN BARU ============ --}}
{{-- =============================================== --}}

@endsection