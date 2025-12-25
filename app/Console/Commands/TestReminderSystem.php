<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\Order\Models\Order;
use App\Domains\Customer\Models\Customer;
use App\Domains\Product\Models\Product;
use App\Domains\Reminder\Models\Reminder;
use App\Domains\Reminder\Models\ReminderLog;
use App\Domains\Reminder\Services\ReminderService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestReminderSystem extends Command
{
    protected $signature = 'test:reminder {--reset : Reset test data}';
    protected $description = 'Test reminder system dengan data dummy';

    protected $reminderService;

    public function __construct(ReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    public function handle()
    {
        if ($this->option('reset')) {
            $this->resetTestData();
            return 0;
        }

        $this->info('🧪 TESTING REMINDER SYSTEM');
        $this->newLine();

        // Step 1: Check existing reminder rules
        $this->info('📋 Step 1: Checking Reminder Rules...');
        $reminders = Reminder::with('product')->active()->get();
        
        if ($reminders->isEmpty()) {
            $this->warn('⚠️  Tidak ada reminder rule yang aktif!');
            
            if ($this->confirm('Buat reminder rule untuk testing?', true)) {
                $this->createTestReminder();
                $reminders = Reminder::with('product')->active()->get();
            } else {
                $this->error('Tidak bisa lanjut tanpa reminder rule.');
                return 1;
            }
        }
        
        $tableData = [];
        foreach ($reminders as $r) {
            $tableData[] = [
                $r->id,
                $r->name,
                $r->product ? $r->product->name : 'Semua Produk',
                "H+{$r->days_after_delivery}",
                $r->send_time ?? '09:00:00'
            ];
        }
        
        $this->table(
            ['ID', 'Nama', 'Produk', 'Hari', 'Waktu Kirim'],
            $tableData
        );
        $this->newLine();

        // Step 2: Create or use existing order
        $this->info('📦 Step 2: Creating Test Order...');
        $testOrder = $this->createTestOrder();
        $this->info("✓ Order created: {$testOrder->order_number}");
        $this->info("  Customer: {$testOrder->customer->name}");
        $this->info("  Phone: {$testOrder->customer->phone}");
        $this->newLine();

        // Step 3: Generate reminder logs
        $this->info('🔄 Step 3: Generating Reminder Logs...');
        
        // Set delivered_at to past so reminder is due
        $testOrder->delivered_at = now()->subMinutes(5);
        $testOrder->save();
        
        $this->reminderService->generateRemindersForOrder($testOrder);
        
        // Get created logs and make them due NOW
        $logs = ReminderLog::where('order_id', $testOrder->id)->get();
        
        foreach ($logs as $log) {
            // Set scheduled_at ke 1 menit yang lalu agar langsung jatuh tempo
            $log->scheduled_at = now()->subMinute();
            $log->save();
            
            $this->info("✓ Reminder log created: ID {$log->id}");
            $this->info("  Reminder: {$log->reminder->name}");
            $this->info("  Scheduled: {$log->scheduled_at}");
            $this->info("  Status: {$log->status}");
        }
        $this->newLine();

        // Step 4: Check pending reminders
        $this->info('⏰ Step 4: Checking Pending Reminders...');
        $pending = ReminderLog::with(['customer', 'reminder'])
            ->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();
        
        $this->info("Found {$pending->count()} pending reminder(s) ready to send");
        $this->newLine();

        // Step 5: Send reminders
        if ($pending->isNotEmpty()) {
            if ($this->confirm('🚀 Send reminders sekarang?', true)) {
                $this->info('Sending reminders...');
                $this->call('reminders:send', ['--dry-run' => true]);
                $this->newLine();
                
                if ($this->confirm('Send for real (kirim ke WhatsApp)?', false)) {
                    $this->call('reminders:send');
                } else {
                    $this->warn('Skipped real sending. Gunakan: php artisan reminders:send');
                }
            }
        } else {
            $this->warn('⚠️  Tidak ada reminder yang jatuh tempo!');
        }

        $this->newLine();
        $this->info('✅ Testing complete!');
        $this->info('💡 Tip: Gunakan --reset untuk hapus data test');
        
        return 0;
    }

    protected function createTestReminder()
    {
        $this->info('Creating test reminder rule...');
        
        $reminder = Reminder::create([
            'name' => 'Test Reminder (Auto-generated)',
            'product_id' => null, // Berlaku untuk semua produk
            'days_after_delivery' => 0, // H+0
            'send_time' => now()->addMinutes(2)->format('H:i:s'), // 2 menit dari sekarang
            'message_template' => 'Halo {customer_name}! Ini pesan test reminder untuk produk {product_name}. Terima kasih sudah berbelanja! 🎉',
        ]);

        $this->info("✓ Created reminder: {$reminder->name}");
    }

    protected function createTestOrder()
    {
        // Cari atau buat customer test
        $customer = Customer::firstOrCreate(
            ['phone' => '081234567890'],
            [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'address' => 'Test Address'
            ]
        );

        // Cari produk pertama
        $product = Product::first();
        
        if (!$product) {
            $this->error('Tidak ada produk di database! Buat produk dulu.');
            exit(1);
        }

        // Buat order
        $order = Order::create([
            'order_number' => 'TEST-' . strtoupper(uniqid()),
            'customer_id' => $customer->id,
            'total_amount' => 100000,
            'status' => 'completed',
            'delivered_at' => now(), // Will be updated later
            'notes' => 'Test order untuk reminder system'
        ]);

        // Buat order item using Eloquent
        $orderItem = new \App\Domains\Order\Models\OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->product_id = $product->id;
        $orderItem->product_name = $product->name;
        $orderItem->quantity = 1;
        $orderItem->price = 100000;
        $orderItem->subtotal = 100000;
        $orderItem->save();

        return $order->load(['customer', 'items']);
    }

    protected function resetTestData()
    {
        $this->warn('🗑️  Resetting test data...');
        
        // Hapus reminder logs dari test orders
        $testOrders = Order::where('order_number', 'LIKE', 'TEST-%')->pluck('id');
        ReminderLog::whereIn('order_id', $testOrders)->delete();
        
        // Hapus test orders
        DB::table('order_items')->whereIn('order_id', $testOrders)->delete();
        Order::where('order_number', 'LIKE', 'TEST-%')->delete();
        
        // Hapus test reminders
        Reminder::where('name', 'LIKE', '%Test Reminder%')->delete();
        
        // Hapus test customer (optional)
        Customer::where('phone', '081234567890')->delete();
        
        $this->info('✓ Test data cleared!');
    }
}
