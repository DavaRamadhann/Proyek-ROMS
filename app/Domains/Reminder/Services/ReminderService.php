<?php

namespace App\Domains\Reminder\Services;

use App\Domains\Reminder\Models\Reminder;
use App\Domains\Reminder\Models\ReminderLog;
use App\Domains\Order\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReminderService
{
    /**
     * Generate reminder logs untuk order yang baru delivered
     */
    /**
     * Generate reminder logs untuk order yang baru delivered
     */
    public function generateRemindersForOrder(Order $order): void
    {
        // Ambil semua reminder yang aktif
        $reminders = Reminder::active()->get();

        // Load order items untuk pengecekan produk
        $order->load('items');
        $orderProductIds = $order->items->pluck('product_id')->toArray(); // Asumsi OrderItem punya product_id (perlu dicek)

        foreach ($reminders as $reminder) {
            // Logic Pengecekan Produk:
            // Jika reminder spesifik untuk produk tertentu, cek apakah order ini mengandung produk tersebut
            if ($reminder->product_id && !in_array($reminder->product_id, $orderProductIds)) {
                continue;
            }

            // Hitung scheduled_at
            $scheduledAt = $order->delivered_at->addDays($reminder->days_after_delivery);

            // Buat reminder log
            ReminderLog::create([
                'reminder_id' => $reminder->id,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Process template dengan replace variable
     */
    public function processTemplate(string $template, array $data): string
    {
        $replacements = [
            '{customer_name}' => $data['customer_name'] ?? '',
            '{product_name}' => $data['product_name'] ?? '',
            '{order_date}' => $data['order_date'] ?? '',
            '{days_since}' => $data['days_since'] ?? '',
            '{recommendation}' => $data['recommendation'] ?? '', // New Variable
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
    }

    /**
     * Kirim reminder via WhatsApp
     */
    public function sendReminder(ReminderLog $log): bool
    {
        try {
            $reminder = $log->reminder;
            $customer = $log->customer;
            $order = $log->order;

            // Load items & products untuk mengambil nama produk & rekomendasi
            $order->load('items.product'); // Eager load product di dalam items

            // Tentukan produk utama & rekomendasi
            // Jika reminder spesifik, gunakan produk itu. Jika tidak, ambil produk pertama dari order.
            $mainProduct = null;
            $recommendationText = '';

            if ($reminder->product_id) {
                // Cari item yang sesuai dengan reminder
                $item = $order->items->firstWhere('product_id', $reminder->product_id);
                $mainProduct = $item ? $item->product : null;
            } else {
                // Ambil item pertama
                $item = $order->items->first();
                $mainProduct = $item ? $item->product : null;
            }

            if ($mainProduct) {
                $productName = $mainProduct->name;
                $recommendationText = $mainProduct->recommendation_text ?? '';
            } else {
                $productName = 'Pesanan Anda';
            }

            // Prepare data untuk template
            $data = [
                'customer_name' => $customer->name,
                'product_name' => $productName,
                'order_date' => $order->created_at->format('d M Y'),
                'days_since' => $order->created_at->diffInDays(now()),
                'recommendation' => $recommendationText,
            ];

            // Process template
            $message = $this->processTemplate($reminder->message_template, $data);

            // Kirim via WhatsApp Service
            $waUrl = rtrim(config('services.whatsapp.url'), '/');
            $apiKey = config('services.whatsapp.api_key');

            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'Accept' => 'application/json',
            ])->post("{$waUrl}/messages/send", [
                'phone' => $customer->phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $log->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'message_sent' => $message,
                ]);

                return true;
            } else {
                $log->update([
                    'status' => 'failed',
                    'error_message' => $response->body(),
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('Reminder send error: ' . $e->getMessage());

            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Ambil reminder yang perlu dikirim hari ini
     */
    public function getPendingReminders()
    {
        return ReminderLog::with(['reminder', 'customer', 'order'])
            ->pending()
            ->scheduled()
            ->get();
    }
}
