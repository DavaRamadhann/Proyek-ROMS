<?php

namespace App\Domains\Message\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'type',
        'variables',
    ];

    protected $casts = [
        'variables' => 'array',
    ];
}
