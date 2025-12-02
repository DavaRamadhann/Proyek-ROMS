<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Message\Models\MessageTemplate;

class CrossSellTemplateSeeder extends Seeder
{
    public function run()
    {
        MessageTemplate::create([
            'name' => 'Reminder dengan Cross-sell',
            'type' => 'reminder',
            'content' => "Halo {customer_name}, terima kasih sudah membeli {product_name} pada {order_date}.\n\nBagaimana stoknya? Sudah mau habis?\n\n{recommendation}\n\nYuk order lagi sebelum kehabisan!",
            'variables' => ['{customer_name}', '{product_name}', '{order_date}', '{recommendation}'],
        ]);
    }
}
