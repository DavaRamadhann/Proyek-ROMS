<?php

namespace App\Domains\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatContact extends Model
{
    protected $table = 'chat_contacts';

    protected $fillable = [
        'phone',
        'name',
    ];

    public function chatRooms(): HasMany
    {
        return $this->hasMany(ChatRoom::class);
    }
}
