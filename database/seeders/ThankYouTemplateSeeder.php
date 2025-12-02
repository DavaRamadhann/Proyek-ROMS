<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Message\Models\MessageTemplate;

class ThankYouTemplateSeeder extends Seeder
{
    public function run()
    {
        MessageTemplate::create([
            'name' => 'Konfirmasi Pesanan Otomatis',
            'type' => 'order_confirmation',
            'content' => "Halo {customer_name} 👋,\n\nTerima kasih sudah berbelanja di SOMEAH!\n\nPesanan Anda *{order_number}* senilai *{total_amount}* sudah kami terima pada {order_date}.\n\nKami akan segera memproses dan mengirimkan pesanan Anda. Tunggu kabar selanjutnya ya!\n\nSalam,\nTim SOMEAH",
            'variables' => ['{customer_name}', '{order_number}', '{total_amount}', '{order_date}'],
        ]);
    }
}
