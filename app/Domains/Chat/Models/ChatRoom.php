<?php
// app/Domains/Chat/Models/ChatRoom.php

namespace App\Domains\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Customer\Models\Customer;
use App\Models\User; // Model User dari Auth bawaan Laravel

class ChatRoom extends Model
{   
    public $timestamps = false;

    protected $table = 'chat_rooms';

    protected $fillable = [
        'customer_id',
        'cs_user_id',
        'status', // e.g., 'new', 'open', 'closed'
    ];

    /**
     * Relasi ke Pelanggan (Customer) yang memiliki room ini.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi ke CS (User) yang menangani room ini.
     */
    public function csUser(): BelongsTo
    {
        // Perhatikan, kita merujuk ke 'cs_user_id'
        // dan ke model 'User' dari namespace Auth
        return $this->belongsTo(User::class, 'cs_user_id');
    }

    /**
     * Relasi ke semua pesan di dalam room ini.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}