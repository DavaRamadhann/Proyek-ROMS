@extends('layout.app')

@section('title', 'Chat Inbox')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3>💬 Inbox Pesan Pelanggan</h3>
                <p class="mb-0">Daftar chat yang di-assign kepada Anda.</p>
            </div>
            
            <div class="card-body p-0">
                {{-- Kita gunakan List Group dari Bootstrap --}}
                <div class="list-group list-group-flush">
                    
                    {{-- 
                      Looping data $rooms yang dikirim dari ChatController.
                      @forelse adalah @foreach + @if(empty) 
                    --}}
                    @forelse ($rooms as $room)
                        {{-- Setiap item adalah link ke halaman chat.show --}}
                        <a href="{{ route('chat.show', $room->id) }}" class="list-group-item list-group-item-action p-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    {{-- Tampilkan nama Customer dari relasi yg sudah di-load --}}
                                    {{ $room->customer->name }}
                                </h5>
                                <small class="text-muted">
                                    {{-- Tampilkan waktu update terakhir --}}
                                    {{ $room->updated_at->diffForHumans() }}
                                </small>
                            </div>
                            <p class="mb-1">
                                {{-- Tampilkan nomor HP Customer --}}
                                <small>{{ $room->customer->phone }}</small>
                            </p>
                            
                            {{-- Beri 'badge' (label) berdasarkan status room --}}
                            @if ($room->status == 'new')
                                <span class="badge bg-danger">BARU</span>
                            @elseif ($room->status == 'open')
                                <span class="badge bg-primary">Open</span>
                            @endif
                        </a>
                        
                    @empty
                        {{-- Bagian ini akan tampil jika $rooms kosong --}}
                        <div class="list-group-item p-5 text-center">
                            <h5 class="text-muted">🎉 Inbox Kosong!</h5>
                            <p class="text-muted">Belum ada chat baru yang di-assign kepada Anda.</p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</div>
@endsection