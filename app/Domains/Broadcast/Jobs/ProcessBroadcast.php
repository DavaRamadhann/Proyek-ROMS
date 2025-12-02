<?php

namespace App\Domains\Broadcast\Jobs;

use App\Domains\Broadcast\Models\Broadcast;
use App\Domains\Broadcast\Models\BroadcastLog;
use App\Domains\Customer\Models\Customer;
use App\Services\WaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $broadcast;

    public function __construct(Broadcast $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    public function handle(WaService $waService)
    {
        $this->broadcast->update(['status' => 'processing']);

        // Segmentation Logic
        $query = Customer::query();
        $segment = $this->broadcast->target_segment;

        if ($segment === 'loyal') {
            // Loyal: Lebih dari 2 pesanan
            $query->has('orders', '>', 2);
        } elseif ($segment === 'inactive') {
            // Inactive: Tidak ada pesanan dalam 90 hari terakhir
            // Atau belum pernah pesan sama sekali tapi sudah terdaftar lama (> 90 hari)
            $query->whereDoesntHave('orders', function($q) {
                $q->where('created_at', '>=', now()->subDays(90));
            });
        } elseif ($segment === 'new') {
            // New: Terdaftar dalam 30 hari terakhir
             $query->where('created_at', '>=', now()->subDays(30));
        }
        // 'all' -> no filter

        $customers = $query->get();

        $this->broadcast->update(['total_recipients' => $customers->count()]);

        foreach ($customers as $customer) {
            try {
                // Ganti variabel dalam pesan
                $message = $this->broadcast->message_content;
                $message = str_replace('{name}', $customer->name, $message);
                $message = str_replace('{customer_name}', $customer->name, $message);

                // Kirim pesan via WaService dengan attachment
                $waService->sendMessage(
                    $customer->phone, 
                    $message, 
                    $this->broadcast->attachment_url
                );

                // Log sukses
                BroadcastLog::create([
                    'broadcast_campaign_id' => $this->broadcast->id,
                    'customer_id' => $customer->id,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $this->broadcast->increment('success_count');

                // Delay untuk menghindari blokir (misal 2 detik)
                sleep(2);

            } catch (\Exception $e) {
                // Log gagal
                BroadcastLog::create([
                    'broadcast_campaign_id' => $this->broadcast->id,
                    'customer_id' => $customer->id,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $this->broadcast->increment('fail_count');
                Log::error("Broadcast failed for customer {$customer->id}: " . $e->getMessage());
            }
        }

        $this->broadcast->update(['status' => 'completed']);
    }
}
