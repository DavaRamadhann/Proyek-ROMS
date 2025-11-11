<?php
// app/Domains/Customer/Models/Customer.php

namespace App\Domains\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'city',
        'segment_tag',
    ];

    /**
     * Relasi ke ChatRooms.
     */
    public function chatRooms(): HasMany
    {
        // Pastikan namespace-nya benar menunjuk ke Model ChatRoom
        return $this->hasMany(\App\Domains\Chat\Models\ChatRoom::class);
    }

    /**
     * Relasi ke Orders.
     */
    public function orders(): HasMany
    {
        // Kita siapkan juga relasi ke Order
        return $this->hasMany(\App\Domains\Order\Models\Order::class);
    }
}