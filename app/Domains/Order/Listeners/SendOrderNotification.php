<?php

namespace App\Domains\Order\Listeners;

use App\Domains\Order\Events\OrderCreated;
use App\Services\WaService;
use App\Domains\Message\Models\MessageTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $waService;

    /**
     * Create the event listener.
     */
    public function __construct(WaService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $customer = $order->customer;

        Log::info("Processing Order Notification for Order: {$order->order_number}");

        try {
            // 1. Ambil Template "Order Confirmation" atau "Thank You"
            // Kita cari template yang tipe-nya 'order_confirmation' atau ambil default
            $template = MessageTemplate::where('type', 'order_confirmation')->first();
            
            if (!$template) {
                // Fallback content jika template belum dibuat
                $message = "Halo {$customer->name},\n\nTerima kasih atas pesanan Anda ({$order->order_number}).\nTotal: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\nPesanan akan segera kami proses.\n\nSalam,\nSOMEAH";
            } else {
                $message = $template->content;
            }

            // 2. Replace Variables
            $replacements = [
                '{customer_name}' => $customer->name,
                '{order_number}' => $order->order_number,
                '{total_amount}' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                '{order_date}' => $order->created_at->format('d M Y H:i'),
            ];

            $message = str_replace(array_keys($replacements), array_values($replacements), $message);

            // 3. Kirim Pesan
            $this->waService->sendMessage($customer->phone, $message);
            
            Log::info("Order Notification sent to {$customer->phone}");

        } catch (\Exception $e) {
            Log::error("Failed to send Order Notification: " . $e->getMessage());
        }
    }
}
