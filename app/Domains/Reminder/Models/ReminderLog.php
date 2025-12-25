<?php

namespace App\Domains\Reminder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Order\Models\Order;
use App\Domains\Customer\Models\Customer;

class ReminderLog extends Model
{
    protected $fillable = [
        'reminder_id',
        'order_id',
        'customer_id',
        'scheduled_at',
        'sent_at',
        'status',
        'message_sent',
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function reminder(): BelongsTo
    {
        return $this->belongsTo(Reminder::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeScheduled($query)
    {
        return $query->where('scheduled_at', '<=', now());
    }
}
