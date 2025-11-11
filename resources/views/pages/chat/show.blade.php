@extends('layout.app')

@section('title', 'Chat dengan ' . $room->customer->name)

{{-- 
  Kita tambahkan sedikit CSS kustom di sini untuk membuat 'chat bubble'.
  Ini adalah cara cepat tanpa file CSS eksternal.
--}}
<style>
    .chat-container {
        max-height: 60vh;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    .chat-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 20px;
        margin-bottom: 10px;
        word-wrap: break-word;
    }
    .chat-bubble.customer {
        background-color: #f1f0f0; /* Abu-abu untuk pelanggan */
        align-self: flex-start;
        border-bottom-left-radius: 0;
    }
    .chat-bubble.cs {
        background-color: #0d6efd; /* Biru (primary) untuk CS */
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 0;
    }
    .chat-bubble .timestamp {
        font-size: 0.75rem;
        color: #999;
        margin-top: 5px;
    }
    .chat-bubble.cs .timestamp {
        color: #e0e0e0;
    }
</style>

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            
            {{-- HEADER KARTU: Info Pelanggan --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Chat dengan: {{ $room->customer->name }}</h5>
                    <small class="text-muted">{{ $room->customer->phone }}</small>
                </div>
                <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary btn-sm">
                    &laquo; Kembali ke Inbox
                </a>
            </div>

            {{-- BODY KARTU: Riwayat Chat --}}
            <div class="card-body p-4 chat-container" id="chat-box">
                
                {{-- Loop semua pesan dari $messages --}}
                @forelse ($messages as $message)
                    
                    {{-- Cek pengirim untuk menentukan style bubble --}}
                    @if ($message->sender_type == 'customer')
                        <div class="chat-bubble customer">
                            {{ $message->message_content }}
                            <div class="timestamp text-start">
                                {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    @else
                        <div class="chat-bubble cs">
                            {{ $message->message_content }}
                            <div class="timestamp text-end">
                                {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    @endif

                @empty
                    <div class="text-center text-muted">
                        <p>Belum ada pesan di room ini.</p>
                        <p>Mulai percakapan!</p>
                    </div>
                @endforelse

            </div>

            {{-- FOOTER KARTU: Form Kirim Pesan --}}
            <div class="card-footer">
                
                {{-- Tampilkan notifikasi sukses/error (jika ada) --}}
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Form ini akan submit ke ChatController@storeMessage --}}
                <form action="{{ route('chat.store', $room->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <textarea name="message_body" class="form-control" rows="2" 
                                  placeholder="Ketik balasan Anda..." required></textarea>
                        <button class="btn btn-primary" type="submit" id="button-send">
                            Kirim &raquo;
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- 
  Script simpel untuk auto-scroll ke pesan terakhir
  Kita bisa letakkan ini di @push('scripts') jika layout Anda punya @stack('scripts')
--}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endsection