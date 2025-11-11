<?php
// app/Providers/RepositoryServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// == Impor semua Interface dan Repository kita di sini ==

// Customer (dari Langkah 0)
use App\Domains\Customer\Interfaces\CustomerRepositoryInterface;
use App\Domains\Customer\Repositories\CustomerRepository;

// ChatRoom (dari Langkah 2)
use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Chat\Repositories\ChatRoomRepository;

// ChatMessage (dari Langkah 3)
use App\Domains\Chat\Interfaces\ChatMessageRepositoryInterface;
use App\Domains\Chat\Repositories\ChatMessageRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Customer
        $this->app->bind(
            CustomerRepositoryInterface::class,
            CustomerRepository::class
        );

        // Bind ChatRoom
        $this->app->bind(
            ChatRoomRepositoryInterface::class,
            ChatRoomRepository::class
        );

        // Bind ChatMessage
        $this->app->bind(
            ChatMessageRepositoryInterface::class,
            ChatMessageRepository::class
        );

        // Nanti Repository lain (Order, Reminder) kita daftarkan di sini juga
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}