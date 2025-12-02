<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessScheduledBroadcasts extends Command
{
    protected $signature = 'broadcast:process-scheduled';
    protected $description = 'Process scheduled broadcasts that are due';

    public function handle()
    {
        $now = now();
        $this->info("Checking for scheduled broadcasts due before {$now}...");

        $broadcasts = \App\Domains\Broadcast\Models\Broadcast::where('status', 'scheduled')
            ->where('scheduled_at', '<=', $now)
            ->get();

        if ($broadcasts->isEmpty()) {
            $this->info('No scheduled broadcasts found.');
            return;
        }

        foreach ($broadcasts as $broadcast) {
            $this->info("Processing broadcast: {$broadcast->name} (ID: {$broadcast->id})");
            
            // Update status to prevent double processing
            $broadcast->update(['status' => 'processing']);
            
            // Dispatch Job
            \App\Domains\Broadcast\Jobs\ProcessBroadcast::dispatch($broadcast);
        }

        $this->info("Processed {$broadcasts->count()} broadcasts.");
    }
}
