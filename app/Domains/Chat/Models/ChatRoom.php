<?php

namespace App\Domains\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Customer\Models\Customer;
use App\Models\User; 

class ChatRoom extends Model
{   
    // 1. HAPUS atau KOMEN baris ini jika tabelmu punya kolom created_at & updated_at
    // public $timestamps = false; 

    protected $table = 'chat_rooms';

    protected $fillable = [
        'customer_id',
        'cs_user_id',
        'status', 
    ];

    // 2. ATAU, jika kamu tetep mau timestamps = false tapi mau format tanggal jalan:
    // Tambahkan casting ini agar string diubah jadi objek Carbon/Datetime
    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // ... relasi lainnya tetap sama
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function csUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cs_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}