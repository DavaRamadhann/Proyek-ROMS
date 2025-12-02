<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Customer\Models\Customer;
use App\Domains\Order\Models\Order;

class BroadcastTestSeeder extends Seeder
{
    public function run()
    {
        // List nomor dari user (Format 08 -> 62)
        $targets = [
            ['phone' => '6285952712346', 'name' => 'Anggota 1 (Loyal)', 'type' => 'loyal'],
            ['phone' => '6283870405395', 'name' => 'Anggota 2 (Big Spender)', 'type' => 'big_spender'],
            ['phone' => '6285924539033', 'name' => 'Anggota 3 (Inactive)', 'type' => 'inactive'],
            ['phone' => '6289515670620', 'name' => 'Anggota 4 (New)', 'type' => 'new'],
        ];

        foreach ($targets as $target) {
            $customer = Customer::firstOrCreate(
                ['phone' => $target['phone']],
                ['name' => $target['name']]
            );

            // Reset orders agar bersih saat testing ulang
            $customer->orders()->delete();

            // Buat dummy order sesuai tipe segmen
            if ($target['type'] === 'loyal') {
                // > 3 Order
                for ($i = 0; $i < 4; $i++) {
                    Order::create([
                        'order_number' => 'ORD-L-' . $customer->id . '-' . $i,
                        'customer_id' => $customer->id,
                        'total_amount' => 100000,
                        'status' => 'completed',
                        'created_at' => now()->subDays(rand(1, 30))
                    ]);
                }
            } elseif ($target['type'] === 'big_spender') {
                // > 1 Juta
                Order::create([
                    'order_number' => 'ORD-B-' . $customer->id,
                    'customer_id' => $customer->id,
                    'total_amount' => 1500000,
                    'status' => 'completed',
                    'created_at' => now()->subDays(5)
                ]);
            } elseif ($target['type'] === 'inactive') {
                // Last order > 60 hari lalu
                Order::create([
                    'order_number' => 'ORD-I-' . $customer->id,
                    'customer_id' => $customer->id,
                    'total_amount' => 50000,
                    'status' => 'completed',
                    'created_at' => now()->subDays(90)
                ]);
            }
            // New Member: Tidak ada order atau baru join (default)
        }
    }
}
