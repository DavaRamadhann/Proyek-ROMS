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
        'days_after_delivery',
        'message_template',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'days_after_delivery' => 'integer',
    ];

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
        return $query->where('is_active', true);
    }
}
