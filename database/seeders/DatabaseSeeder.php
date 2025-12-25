<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        \App\Models\User::create([
            'name' => 'Admin SOMEAH',
            'email' => 'admin@someah.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create default CS user
        \App\Models\User::create([
            'name' => 'CS SOMEAH',
            'email' => 'cs@someah.com',
            'password' => bcrypt('cs123'),
            'role' => 'cs',
            'email_verified_at' => now(),
        ]);


        // Call other seeders
        $this->call([
            ThankYouTemplateSeeder::class,
            CrossSellTemplateSeeder::class,
        ]);
    }
}
