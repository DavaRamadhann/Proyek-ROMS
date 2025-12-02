<?php

namespace App\Domains\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'city',
        'address', // Added address
        'segment_tag',
        'is_manual_name',
    ];

    protected $appends = ['segment'];

    /**
     * Accessor untuk mendapatkan nomor HP yang bersih (tanpa @c.us)
     */
    public function getCleanPhoneAttribute()
    {
        return str_replace('@c.us', '', $this->phone);
    }

    /**
     * Accessor Real-time Segmentation (RFM)
     * Menghitung status pelanggan secara dinamis berdasarkan history order.
     */
    public function getSegmentAttribute()
    {
        // Jika ada tag manual, gunakan itu dulu (opsional, tapi kita prioritaskan logic otomatis sesuai request)
        // if ($this->segment_tag) return $this->segment_tag;

        // Load orders jika belum di-load (lazy loading)
        // Hati-hati N+1 query jika meloop banyak customer, tapi aman untuk detail view
        $orders = $this->orders; 

        if ($orders->isEmpty()) {
            // Cek umur akun
            $daysSinceJoined = $this->created_at ? $this->created_at->diffInDays(now()) : 0;
            return $daysSinceJoined < 30 ? 'New Member' : 'Regular';
        }

        $totalSpend = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $lastOrderDate = $orders->sortByDesc('created_at')->first()->created_at;
        $daysSinceLastOrder = $lastOrderDate ? $lastOrderDate->diffInDays(now()) : 999;

        // 1. Big Spender (> 1 Juta)
        if ($totalSpend > 1000000) {
            return 'Big Spender';
        }

        // 2. Loyal (> 3 Order)
        if ($totalOrders > 3) {
            return 'Loyal';
        }

        // 3. Inactive (Gak belanja > 60 hari)
        if ($daysSinceLastOrder > 60) {
            return 'Inactive';
        }

        // 4. New Member (< 30 hari gabung)
        $daysSinceJoined = $this->created_at ? $this->created_at->diffInDays(now()) : 0;
        if ($daysSinceJoined < 30) {
            return 'New Member';
        }

        return 'Regular';
    }

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