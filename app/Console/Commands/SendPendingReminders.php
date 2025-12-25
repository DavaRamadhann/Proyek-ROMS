<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\Reminder\Models\ReminderLog;
use App\Services\WaService;
use Illuminate\Support\Facades\Log;

class SendPendingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send {--dry-run : Hanya simulasi, tidak kirim pesan asli}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder yang sudah jatuh tempo';

    /**
     * Execute the console command.
     */
    public function handle(WaService $waService)
    {
        $this->info('Mengecek reminder yang jatuh tempo...');

        $dueReminders = ReminderLog::with(['customer', 'reminder'])
            ->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($dueReminders->isEmpty()) {
            $this->info('Tidak ada reminder yang perlu dikirim.');
            return;
        }

        $this->info("Ditemukan {$dueReminders->count()} reminder.");

        $bar = $this->output->createProgressBar($dueReminders->count());
        $bar->start();

        foreach ($dueReminders as $log) {
            try {
                // Replace variable
                $message = $log->reminder->message_template;
                $message = str_replace('{name}', $log->customer->name, $message);
                $message = str_replace('{customer_name}', $log->customer->name, $message);
                
                // Reminder specific variables
                if ($log->order) {
                    $message = str_replace('{order_number}', $log->order->order_number, $message);
                    $message = str_replace('{order_date}', $log->order->created_at->format('d M Y'), $message);
                }

                if ($log->reminder->product) {
                    $message = str_replace('{product_name}', $log->reminder->product->name, $message);
                } else {
                    // Fallback jika reminder untuk semua produk, ambil dari order items pertama
                    $productName = $log->order->items->first()->product_name ?? 'Produk kami';
                    $message = str_replace('{product_name}', $productName, $message);
                }

                $daysSince = (int) $log->order->created_at->diffInDays(now());
                $message = str_replace('{days_since}', $daysSince, $message);

                if ($this->option('dry-run')) {
                    $this->line(" [DRY-RUN] Kirim ke {$log->customer->phone}: $message");
                } else {
                    // Kirim WA
                    $waService->sendMessage($log->customer->phone, $message);

                    // Update Log
                    $log->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'message_sent' => $message,
                    ]);
                }

                // Delay aman
                if (!$this->option('dry-run')) {
                    sleep(2);
                }

            } catch (\Exception $e) {
                Log::error("Gagal kirim reminder ID {$log->id}: " . $e->getMessage());
                
                if (!$this->option('dry-run')) {
                    $log->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Selesai memproses reminder.');
    }
}
