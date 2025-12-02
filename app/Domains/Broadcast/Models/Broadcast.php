<?php

namespace App\Domains\Broadcast\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Broadcast extends Model
{
    protected $table = 'broadcast_campaigns';

    protected $fillable = [
        'name',
        'message_content',
        'attachment_url',
        'attachment_type',
        'target_segment',
        'scheduled_at',
        'status',
        'total_recipients',
        'success_count',
        'fail_count',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(BroadcastLog::class, 'broadcast_campaign_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
