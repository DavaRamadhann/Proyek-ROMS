<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'verification_code',
        'verification_code_expires_at',
        'reset_code',
        'reset_code_expires_at',
        'is_online',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
        'reset_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'reset_code_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_online' => 'boolean',
            'is_manual_name' => 'boolean', // PostgreSQL boolean compatibility
        ];
    }

    // Helper methods untuk verifikasi
    public function isVerificationCodeExpired(): bool
    {
        return $this->verification_code_expires_at && $this->verification_code_expires_at < now();
    }

    public function isResetCodeExpired(): bool
    {
        return $this->reset_code_expires_at && $this->reset_code_expires_at < now();
    }

    // Role checking methods
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCS(): bool
    {
        return $this->role === 'cs';
    }

    // Relationships - Uncomment setelah model dibuat
    // public function orders()
    // {
    //     return $this->hasMany(Order::class, 'customer_id');
    // }

    // public function chatRooms()
    // {
    //     return $this->hasMany(ChatRoom::class, 'customer_id');
    // }

    // public function chatMessages()
    // {
    //     return $this->hasMany(ChatMessage::class, 'sender_id');
    // }

    /**
     * Chat rooms assigned to this CS user
     */
    public function assignedChats()
    {
        return $this->hasMany(\App\Domains\Chat\Models\ChatRoom::class, 'cs_user_id');
    }
}