<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\Reminder\Models\ReminderLog;
use Carbon\Carbon;

class QuickTestReminder extends Command
{
    protected $signature = 'test:reminder-quick {log_id? : ID reminder log untuk test}';
    protected $description = 'Quick test untuk reminder - manipulasi scheduled_at jadi sekarang';

    public function handle()
    {
        $this->info('🧪 QUICK REMINDER TEST');
        $this->newLine();

        $logId = $this->argument('log_id');

        if ($logId) {
            // Test specific log
            $log = ReminderLog::find($logId);
            
            if (!$log) {
                $this->error("Reminder log #{$logId} tidak ditemukan!");
                return 1;
            }

            $this->testSingleLog($log);
        } else {
            // Find latest pending log
            $log = ReminderLog::where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$log) {
                $this->warn('⚠️  Tidak ada reminder log yang pending.');
                $this->info('Buat reminder dulu lewat UI, lalu jalankan command ini lagi.');
                return 0;
            }

            $this->info("Found latest pending log: #{$log->id}");
            $this->testSingleLog($log);
        }

        return 0;
    }

    protected function testSingleLog($log)
    {
        $log->load(['customer', 'reminder', 'order']);

        $this->info("📋 Reminder Log Details:");
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $log->id],
                ['Customer', $log->customer->name ?? 'N/A'],
                ['Phone', $log->customer->phone ?? 'N/A'],
                ['Reminder', $log->reminder->name ?? 'N/A'],
                ['Order', $log->order->order_number ?? 'N/A'],
                ['Status', $log->status],
                ['Originally Scheduled', $log->scheduled_at],
            ]
        );

        $this->newLine();

        if ($this->confirm('Set scheduled_at ke SEKARANG untuk testing?', true)) {
            $log->scheduled_at = now()->subMinute();
            $log->status = 'pending';
            $log->save();

            $this->info("✅ Updated! Scheduled_at: {$log->scheduled_at}");
            $this->newLine();

            $this->info('Now run:');
            $this->line('  php artisan reminders:send --dry-run   (untuk simulasi)');
            $this->line('  php artisan reminders:send             (untuk kirim real)');
        } else {
            $this->warn('Cancelled.');
        }
    }
}
