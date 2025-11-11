<?php
// app/Domains/Chat/Models/ChatMessage.php

namespace App\Domains\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'chat_room_id',
        'sender_type', // 'customer' or 'cs'
        'message_content',
    ];

    /**
     * Relasi ke Room tempat pesan ini berada.
     */
    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }
}