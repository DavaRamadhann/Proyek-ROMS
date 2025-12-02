@extends('layout.main')

@section('title', 'Chat dengan ' . ($room->customer->name ?? 'Customer'))

@push('styles')
<style>
    /* --- CHAT CONTAINER --- */
    .chat-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 140px); /* Adjust based on header/footer */
        background: #efe7dd; /* WA Default BG */
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .chat-header {
        background: #f0f2f5;
        padding: 10px 20px;
        border-bottom: 1px solid #d1d7db;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }

    .chat-footer {
        background: #f0f2f5;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* --- BUBBLES --- */
    .msg-row { display: flex; margin-bottom: 8px; }
    .msg-row.outgoing { justify-content: flex-end; }
    .msg-row.incoming { justify-content: flex-start; }

    .msg-bubble {
        max-width: 70%;
        padding: 6px 10px 8px 10px;
        border-radius: 8px;
        position: relative;
        font-size: 14.2px;
        line-height: 19px;
        box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
    }

    .msg-bubble.in { background: #fff; border-top-left-radius: 0; }
    .msg-bubble.out { background: #d9fdd3; border-top-right-radius: 0; }

    .msg-time {
        float: right; 
        margin-left: 10px; 
        margin-top: 4px;
        font-size: 11px; 
        color: #667781; 
        display: flex; 
        align-items: center;
        height: 15px;
    }
    
    /* Clearfix for float */
    .msg-bubble::after {
        content: "";
        clear: both;
        display: table;
    }

    /* --- INPUT --- */
    .chat-input {
        flex: 1;
        border: none;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 15px;
        outline: none;
        resize: none;
        overflow-y: hidden;
        min-height: 45px;
        max-height: 120px;
    }
</style>
@endpush

@section('main-content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <div class="chat-wrapper">
            {{-- HEADER --}}
            <div class="chat-header">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" 
                         style="width: 40px; height: 40px; font-weight: bold;">
                        {{ strtoupper(substr($room->customer->name ?? 'G', 0, 1)) }}
                    </div>
                    <div>
                        @php
                            $name = $room->customer->name ?? 'Guest';
                            $phone = $room->customer->phone ?? '-';
                            // Clean Name if it's a WA ID
                            if (str_contains($name, '@c.us') || str_contains($name, '@g.us')) {
                                $name = $phone;
                            }
                        @endphp
                        <h6 class="mb-0 fw-bold">{{ $name }}</h6>
                        <small class="text-muted">{{ $phone }}</small>
                    </div>
                </div>
                <a href="{{ route('chat.ui') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

            {{-- BODY --}}
            <div class="chat-body" id="messages-box">
                @forelse ($messages as $message)
                    @php
                        $isMe = $message->sender_type != 'customer'; // Assuming 'customer' is the other party
                        $align = $isMe ? 'outgoing' : 'incoming';
                        $bubble = $isMe ? 'out' : 'in';
                        $time = $message->created_at ? $message->created_at->format('H:i') : '';
                    @endphp
                    <div class="msg-row {{ $align }}">
                        <div class="msg-bubble {{ $bubble }}">
                            {!! nl2br(e($message->message_content)) !!}
                            <div class="msg-time">
                                {{ $time }}
                                @if($isMe)
                                    <i class="bi bi-check2-all text-primary ms-1"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center mt-5">
                        <span class="badge bg-white text-secondary shadow-sm p-2">Mulai percakapan</span>
                    </div>
                @endforelse
            </div>

            {{-- FOOTER --}}
            <div class="chat-footer">
                <i class="bi bi-emoji-smile fs-4 text-secondary" style="cursor: pointer;"></i>
                <i class="bi bi-paperclip fs-4 text-secondary" style="cursor: pointer;"></i>
                
                <textarea id="input-message" class="chat-input" rows="1" placeholder="Ketik pesan..."></textarea>
                
                <button class="btn btn-primary rounded-circle p-2" style="width: 45px; height: 45px;" onclick="sendMessage()">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>

    </div>
</div>

{{-- Hidden Inputs for JS --}}
<input type="hidden" id="room-id" value="{{ $room->id }}">

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
@endpush