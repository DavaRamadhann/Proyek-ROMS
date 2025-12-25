<?php

namespace App\Domains\Reminder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Product\Models\Product;

class Reminder extends Model
{
    protected $fillable = [
        'name',
        'product_id',
        'cross_sell_product_id',
        'days_after_delivery',
        'send_time',
        'message_template',
        'is_active',
    ];

    protected $casts = [
        'days_after_delivery' => 'integer',
    ];

    /**
     * Accessor untuk memastikan is_active selalu boolean
     * meski tidak dicast di $casts (untuk menghindari issue insert Postgres)
     */
    public function getIsActiveAttribute($value)
    {
        if (is_null($value)) return false;
        if (is_bool($value)) return $value;
        if (is_string($value) && $value === 'f') return false;
        return (bool) $value;
    }

    /**
     * Relasi ke Product (nullable - bisa untuk semua produk)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke ReminderLogs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ReminderLog::class);
    }

    /**
     * Scope untuk reminder yang aktif
     */
    public function scopeActive($query)
    {
        return $query->whereRaw('is_active = true');
    }
}
