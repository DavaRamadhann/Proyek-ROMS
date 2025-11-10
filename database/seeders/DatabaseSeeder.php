<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cek apakah user sudah ada
        if (User::where('email', 'admin@someah.com')->doesntExist()) {
            User::create([
                'name' => 'Admin SOMEAH',
                'email' => 'admin@someah.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }

        if (User::where('email', 'staff@someah.com')->doesntExist()) {
            User::create([
                'name' => 'Staff SOMEAH',
                'email' => 'staff@someah.com',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
            ]);
        }

        if (User::where('email', 'customer@test.com')->doesntExist()) {
            User::create([
                'name' => 'Customer Test',
                'email' => 'customer@test.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
            ]);
        }
    }
}