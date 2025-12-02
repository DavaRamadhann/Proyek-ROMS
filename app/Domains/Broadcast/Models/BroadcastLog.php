<?php

namespace App\Domains\Broadcast\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Customer\Models\Customer;

class BroadcastLog extends Model
{
    protected $fillable = [
        'broadcast_campaign_id',
        'customer_id',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function broadcast(): BelongsTo
    {
        return $this->belongsTo(Broadcast::class, 'broadcast_campaign_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
