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
        $twoMinutesAgo = $now->copy()->subMinutes(2);
        
        $this->info("Checking for scheduled broadcasts due between {$twoMinutesAgo} and {$now}...");

        // Process all scheduled broadcasts that are due
        $broadcasts = \App\Domains\Broadcast\Models\Broadcast::where('status', 'scheduled')
            ->where('scheduled_at', '<=', $now)
            ->orderBy('scheduled_at', 'asc')
            ->get();

        if ($broadcasts->isEmpty()) {
            $this->info('No scheduled broadcasts found.');
            return;
        }

        foreach ($broadcasts as $broadcast) {
            $this->info("Processing broadcast: {$broadcast->name} (ID: {$broadcast->id}) scheduled for {$broadcast->scheduled_at}");
            
            // Update status to prevent double processing
            $broadcast->update(['status' => 'processing']);
            
            // Dispatch Job
            \App\Domains\Broadcast\Jobs\ProcessBroadcast::dispatch($broadcast);
        }

        $this->info("Processed {$broadcasts->count()} broadcasts.");
    }
}
