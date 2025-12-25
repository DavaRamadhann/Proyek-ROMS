@extends('layouts.app')

@section('title', 'Chat dengan ' . ($room->customer->name ?? 'Customer'))

@section('content')

<div class="flex justify-center">
    <div class="w-full max-w-5xl">
        
        {{-- Chat Container with WhatsApp Style --}}
        <div class="flex flex-col h-[calc(100vh-140px)] bg-[#efe7dd] rounded-2xl overflow-hidden shadow-xl border border-slate-200" 
             style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');">
            
            {{-- HEADER --}}
            <div class="bg-[#f0f2f5] border-b border-slate-300 px-5 py-3 flex justify-between items-center flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-400 text-white flex items-center justify-center font-bold text-lg">
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
                        <h6 class="font-bold text-slate-800 text-base">{{ $name }}</h6>
                        <small class="text-slate-500 text-xs">{{ $phone }}</small>
                    </div>
                </div>
                <a href="{{ route('chat.ui') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-slate-200 transition text-slate-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </a>
            </div>

            {{-- BODY - Messages Area --}}
            <div class="flex-1 overflow-y-auto p-5 flex flex-col gap-2 custom-scrollbar" id="messages-box">
                @forelse ($messages as $message)
                    @php
                        $isMe = $message->sender_type != 'customer';
                        $time = $message->created_at ? $message->created_at->format('H:i') : '';
                    @endphp
                    
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%] px-3 py-2 rounded-lg shadow-sm relative {{ $isMe ? 'bg-[#d9fdd3] rounded-tr-none' : 'bg-white rounded-tl-none' }}">
                            <div class="text-sm leading-5 text-slate-800 break-words">
                                {!! nl2br(e($message->message_content)) !!}
                            </div>
                            <div class="flex items-center justify-end gap-1 mt-1 float-right ml-2" style="font-size: 11px; color: #667781;">
                                {{ $time }}
                                @if($isMe)
                                    <i data-lucide="check-check" class="h-3 w-3 text-blue-500"></i>
                                @endif
                            </div>
                            {{-- Clearfix --}}
                            <div class="clear-both"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center mt-10">
                        <span class="inline-block bg-white/90 text-slate-600 shadow-sm px-4 py-2 rounded-lg text-sm">
                            Mulai percakapan
                        </span>
                    </div>
                @endforelse
            </div>

            {{-- FOOTER - Input Area --}}
            <div class="bg-[#f0f2f5] border-t border-slate-300 px-5 py-3 flex items-center gap-3 flex-shrink-0">
                <button class="text-slate-500 hover:text-slate-700 transition" title="Emoji">
                    <i data-lucide="smile" class="h-6 w-6"></i>
                </button>
                <button class="text-slate-500 hover:text-slate-700 transition" title="Attach">
                    <i data-lucide="paperclip" class="h-6 w-6"></i>
                </button>
                
                <textarea id="input-message" 
                    class="flex-1 border-none rounded-lg px-4 py-3 text-sm resize-none outline-none focus:ring-2 focus:ring-[#84994F]/30 bg-white min-h-[45px] max-h-[120px]" 
                    rows="1" 
                    placeholder="Ketik pesan..."></textarea>
                
                <button onclick="sendMessage()" 
                    class="w-11 h-11 flex items-center justify-center rounded-full bg-[#84994F] hover:bg-[#6b7d3f] text-white transition shadow-md">
                    <i data-lucide="send" class="h-5 w-5"></i>
                </button>
            </div>
        </div>

    </div>
</div>

{{-- Hidden Inputs for JS --}}
<input type="hidden" id="room-id" value="{{ $room->id }}">

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Auto-scroll to bottom
        const messagesBox = document.getElementById('messages-box');
        if (messagesBox) {
            messagesBox.scrollTop = messagesBox.scrollHeight;
        }
        
        // Textarea auto-expand
        const textarea = document.getElementById('input-message');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
            
            // Send on Enter
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }
    });
    
    function sendMessage() {
        const input = document.getElementById('input-message');
        const message = input.value.trim();
        const roomId = document.getElementById('room-id').value;
        
        if (!message) return;
        
        // Simple implementation - you can enhance this with AJAX
        console.log('Sending message:', message, 'to room:', roomId);
        
        // Clear input
        input.value = '';
        input.style.height = 'auto';
        
        // TODO: Implement actual send logic via AJAX
        alert('Send message functionality - to be implemented with AJAX');
    }
</script>
@endpush

@endsection