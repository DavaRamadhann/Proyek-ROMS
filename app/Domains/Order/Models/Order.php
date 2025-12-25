<?php

namespace App\Domains\Order\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Customer\Models\Customer;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'total_amount',
        'status',
        'payment_method',
        'shipping_address',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reminderLogs()
    {
        return $this->hasMany(\App\Domains\Reminder\Models\ReminderLog::class);
    }
}
