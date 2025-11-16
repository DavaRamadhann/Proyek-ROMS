<?php
// app/Domains/Chat/Models/ChatMessage.php

namespace App\Domains\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    public $timestamps = false;

    protected $table = 'chat_messages';

    protected $fillable = [
    'chat_room_id',
    'sender_id',
    'sender_type',
    'message_content',
    'status',
    ];

    /**
     * Relasi ke Room tempat pesan ini berada.
     */
    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }
}