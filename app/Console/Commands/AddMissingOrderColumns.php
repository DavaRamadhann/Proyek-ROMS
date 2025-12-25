<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddMissingOrderColumns extends Command
{
    protected $signature = 'fix:order-columns';
    protected $description = 'Add missing shipped_at and delivered_at columns to orders table';

    public function handle()
    {
        try {
            // Check if columns already exist
            $hasShippedAt = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name='orders' AND column_name='shipped_at'");
            $hasDeliveredAt = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name='orders' AND column_name='delivered_at'");
            
            if (empty($hasShippedAt)) {
                $this->info('Adding shipped_at column...');
                DB::statement('ALTER TABLE orders ADD COLUMN shipped_at timestamp NULL');
                $this->info('✓ shipped_at added');
            } else {
                $this->info('✓ shipped_at already exists');
            }
            
            if (empty($hasDeliveredAt)) {
                $this->info('Adding delivered_at column...');
                DB::statement('ALTER TABLE orders ADD COLUMN delivered_at timestamp NULL');
                $this->info('✓ delivered_at added');
            } else {
                $this->info('✓ delivered_at already exists');
            }
            
            $this->info('All done!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
