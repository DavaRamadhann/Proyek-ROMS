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
        // CHECK CONNECTION FIRST to prevent mass failure
        $status = $waService->getConnectionStatus();
        if ($status !== 'CONNECTED') {
            Log::warning("Broadcast delayed: WhatsApp disconnected (Status: $status).");
            
            // Jika queue driver bukan sync, release job agar dicoba lagi nanti
            if (config('queue.default') !== 'sync') {
                $this->release(60); // Coba lagi 1 menit kemudian
                return;
            }
            
            // Jika sync, kita tidak bisa release, jadi biarkan fail atau throw exception
            // Tapi agar user tau, kita update status broadcast jadi 'paused' atau tetap 'processing'
            // lalu throw exception
            throw new \Exception("WhatsApp Disconnected. Mohon pastikan service berjalan dan QR Code sudah discan.");
        }

        // Prevent timeout for long broadcasts (especially in sync mode)
        set_time_limit(0);
        
        $this->broadcast->update(['status' => 'processing']);

        try {
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

            // IDEMPOTENCY: Jangan kirim ke user yang SUDAH menerima pesan ini (status=sent)
            // Ini penting agar jika job di-restart/retry, tidak double sending.
            $query->whereDoesntHave('broadcastLogs', function($q) {
                $q->where('broadcast_campaign_id', $this->broadcast->id)
                  ->where('status', 'sent');
            });

            $customers = $query->get();

            $this->broadcast->update(['total_recipients' => $customers->count()]);

            foreach ($customers as $customer) {
                try {
                    // Ganti variabel dalam pesan
                    $message = $this->broadcast->message_content;
                    $message = str_replace('{name}', $customer->name, $message);
                    $message = str_replace('{customer_name}', $customer->name, $message);

                    // Kirim pesan via WaService dengan attachment
                    $attachmentName = $this->broadcast->attachment_path ? basename($this->broadcast->attachment_path) : null;
                    
                    $waService->sendMessage(
                        $customer->phone, 
                        $message, 
                        $this->broadcast->attachment_url,
                        $this->broadcast->attachment_type,
                        $attachmentName
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
                    // Jika sync, kurangi delay agar tidak terlalu lama menunggu
                    if (config('queue.default') === 'sync') {
                        sleep(1); 
                    } else {
                        sleep(2);
                    }

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

                    // Jika error karena "Client not ready" atau "Disconnected", hentikan loop dan release job
                    if (str_contains($e->getMessage(), 'not ready') || str_contains($e->getMessage(), 'disconnected')) {
                         Log::warning("Aborting broadcast loop due to connection loss.");
                         if (config('queue.default') !== 'sync') {
                             $this->release(30); // Release sisa queue
                             return;
                         }
                         break; // Break loop if sync
                    }
                }
            }

            $this->broadcast->update(['status' => 'completed']);

        } catch (\Exception $e) {
            Log::error("Broadcast Job Failed: " . $e->getMessage());
            // Jangan set failed jika sebagian sudah terkirim, biarkan status processing atau set partially_failed jika ada
            // Tapi untuk simplifikasi, kita biarkan status terakhir atau set ke 'failed' jika fatal
            // $this->broadcast->update(['status' => 'failed']); 
        }
    }
}
