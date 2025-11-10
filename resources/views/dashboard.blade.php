@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Selamat Datang di Dashboard ROMS
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            </div>
            <div class="card-body">
                <p>Halo, <strong>{{ auth()->user()->name }}</strong>!</p>
                <p>Anda login sebagai: <strong>{{ strtoupper(auth()->user()->role) }}</strong></p>
                
                <p>Dari sini, Anda bisa melanjutkan ke modul manajemen pelanggan, pesanan, dan lainnya.</p>
            </div>
        </div>
    </div>
</div>
@endsection