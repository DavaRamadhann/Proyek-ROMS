<?php

namespace App\Domains\Automation\Listeners;

use App\Domains\Order\Models\Order;
use App\Domains\Chat\Services\ChatService;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class OrderEventHandler
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // Asumsi event memiliki properti $order
        if (!isset($event->order)) {
            return;
        }

        $order = $event->order;

        // Logika 1: Kirim Pesan Terima Kasih saat Order Created
        if ($order->wasRecentlyCreated) {
            $this->sendThankYouMessage($order);
        }
    }

    protected function sendThankYouMessage(Order $order)
    {
        try {
            $customer = $order->customer;
            if (!$customer || !$customer->phone) return;

            // Cari Room Chat Customer
            // Kita gunakan repo via ChatService atau langsung (tergantung aksesibilitas)
            // Untuk simplifikasi, kita asumsikan ChatService punya method untuk kirim pesan by phone atau kita cari room dulu
            
            // Cari room ID
            $room = \App\Domains\Chat\Models\ChatRoom::where('customer_id', $customer->id)->first();
            
            if (!$room) {
                // Jika belum ada room, create baru (biasanya sudah dihandle saat inbound, tapi ini outbound first)
                // Logic create room ada di ChatService/Repo, kita bisa panggil repo disini jika perlu
                // Atau skip dulu jika kebijakan hanya kirim ke yang sudah pernah chat
                return; 
            }

            // Pesan Terima Kasih
            $message = "Halo Kak {$customer->name}, terima kasih sudah berbelanja di Someah! \n\n" .
                       "Pesanan #{$order->order_number} sudah kami terima dan akan segera diproses. \n" .
                       "Total: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\n" .
                       "Kami akan kabari lagi saat paket dikirim ya! ☕";

            // Gunakan System User atau Admin sebagai pengirim
            $systemUser = User::where('role', 'admin')->first(); // Fallback sender
            
            if ($systemUser) {
                $this->chatService->sendOutboundMessage($systemUser, $room->id, $message);
                Log::info("Automation: Sent Thank You message to Order #{$order->id}");
            }

        } catch (\Exception $e) {
            Log::error("Automation Error (Thank You Msg): " . $e->getMessage());
        }
    }
}
