<?php

namespace App\Domains\Reminder\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Reminder\Models\ReminderLog;
use Illuminate\Http\Request;

use App\Domains\Reminder\Models\Reminder;
use App\Domains\Product\Models\Product;

class ReminderController extends Controller
{
    public function index()
    {
        // Menampilkan Rules (Aturan)
        $rules = Reminder::with('product')->get();

        // Menampilkan reminder yang akan datang (pending) dan history (sent/failed)
        // [MODIFIED] Return empty paginator initially for AJAX loading
        $reminders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            
        return view('pages.reminder.index', compact('reminders', 'rules'));
    }

    public function create()
    {
        $products = Product::all();
        $templates = \App\Domains\Message\Models\MessageTemplate::whereIn('type', ['general', 'reminder'])->get();
        return view('pages.reminder.create', compact('products', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'nullable',
            'cross_sell_product_id' => 'nullable',
            'days_after_delivery' => 'required|integer|min:0|max:365',
            'send_time' => 'nullable', // Made nullable
            'message_template' => 'required|string',
        ]);

        // Handle nullable foreign keys explicitly
        $validated['product_id'] = $request->product_id ?: null;
        $validated['cross_sell_product_id'] = $request->cross_sell_product_id ?: null;
        
        // Handle checkbox separately
        $isActive = $request->boolean('is_active');

        $reminder = new Reminder();
        $reminder->name = $validated['name'];
        $reminder->product_id = $validated['product_id'];
        $reminder->cross_sell_product_id = $validated['cross_sell_product_id'];
        $reminder->days_after_delivery = $validated['days_after_delivery'];
        // Default to 09:00 if not provided
        $reminder->send_time = $validated['send_time'] ?? '09:00'; 
        $reminder->message_template = $validated['message_template'];
        // [FIX] POSTGRES BOOLEAN ISSUE: Pass as string 'true'/'false' to avoid integer '1' binding
        $reminder->is_active = $isActive ? 'true' : 'false';
        $reminder->save();

        return redirect()->route('reminders.index')->with('success', 'Reminder rule berhasil dibuat.');
    }

    public function edit(Reminder $reminder)
    {
        $products = Product::all();
        $templates = \App\Domains\Message\Models\MessageTemplate::whereIn('type', ['general', 'reminder'])->get();
        return view('pages.reminder.edit', compact('reminder', 'products', 'templates'));
    }

    public function update(Request $request, Reminder $reminder)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'nullable',
            'cross_sell_product_id' => 'nullable',
            'days_after_delivery' => 'required|integer|min:0|max:365',
            'send_time' => 'nullable', // Made nullable
            'message_template' => 'required|string',
        ]);

        // Handle nullable foreign keys explicitly
        $validated['product_id'] = $request->product_id ?: null;
        $validated['cross_sell_product_id'] = $request->cross_sell_product_id ?: null;

        // Handle checkbox separately
        $isActive = $request->boolean('is_active');

        $reminder->name = $validated['name'];
        $reminder->product_id = $validated['product_id'];
        $reminder->cross_sell_product_id = $validated['cross_sell_product_id'];
        $reminder->days_after_delivery = $validated['days_after_delivery'];
        
        // Only update send_time if it's present in the request
        if ($request->has('send_time') && !empty($request->send_time)) {
             $reminder->send_time = $validated['send_time'];
        }

        $reminder->message_template = $validated['message_template'];
        // [FIX] POSTGRES BOOLEAN ISSUE: Pass as string 'true'/'false' to avoid integer '1' binding
        $reminder->is_active = $isActive ? 'true' : 'false';
        $reminder->save();

        // [FIX] Recalculate scheduled_at for all PENDING logs
        // This ensures that if the rule changes (e.g. 30 days -> 5 days), 
        // the pending reminders are updated to the new schedule.
        $pendingLogs = ReminderLog::where('reminder_id', $reminder->id)
            ->where('status', 'pending')
            ->with('order')
            ->get();

        foreach ($pendingLogs as $log) {
            if ($log->order) {
                // Determine base date: Delivery Date or Created Date
                // Assuming 'delivery_date' exists on Order, otherwise fallback to created_at
                $baseDate = $log->order->delivery_date 
                    ? \Carbon\Carbon::parse($log->order->delivery_date) 
                    : $log->order->created_at;

                // Calculate new schedule date
                $newScheduleDate = $baseDate->copy()->addDays($reminder->days_after_delivery);
                
                // Set the time based on reminder settings
                $timeParts = explode(':', $reminder->send_time);
                $newScheduleDate->setTime($timeParts[0], $timeParts[1], 0);

                // Update the log
                $log->scheduled_at = $newScheduleDate;
                $log->save();
            }
        }

        return redirect()->route('reminders.index')->with('success', 'Reminder rule berhasil diperbarui dan jadwal pending telah disesuaikan.');
    }

    public function destroy(ReminderLog $reminder)
    {
        if ($reminder->status != 'pending') {
            return back()->with('error', 'Hanya reminder yang masih pending yang dapat dihapus.');
        }

        $reminder->delete();

        return back()->with('success', 'Reminder berhasil dibatalkan.');
    }

    /**
     * Syncs reminder logs based on active rules and orders, then returns the updated list.
     */
    public function syncAndFetch()
    {
        // Set a higher time limit for this specific request if possible, 
        // though optimization is the real fix.
        set_time_limit(120); 

        $rules = Reminder::active()->get();

        // Optimization: Only look back X days to prevent scanning entire history
        // Assuming reminders are relevant for recent orders (e.g. last 3 months)
        $lookbackDate = now()->subDays(90);

        foreach ($rules as $rule) {
            // Find qualifying orders: 
            // 1. Delivered
            // 2. Delivered after lookback date
            // 3. Does NOT have a log for this reminder yet
            $query = \App\Domains\Order\Models\Order::whereNotNull('delivered_at')
                ->where('delivered_at', '>=', $lookbackDate);
            
            if ($rule->product_id) {
                $query->whereHas('items', function($q) use ($rule) {
                    $q->where('product_id', $rule->product_id);
                });
            }

            // Exclude orders that already have a log for this rule
            // This prevents the N+1 check inside the loop
            $query->whereDoesntHave('reminderLogs', function($q) use ($rule) {
                $q->where('reminder_id', $rule->id);
            });

            // Chunking to handle memory efficiently if there are still many matches
            $query->chunk(100, function($orders) use ($rule) {
                foreach ($orders as $order) {
                    // Create new log
                    $scheduledAt = $order->delivered_at->copy()->addDays($rule->days_after_delivery);
                    
                    // Set time
                    $timeParts = explode(':', $rule->send_time ?? '09:00');
                    $scheduledAt->setTime($timeParts[0], $timeParts[1], 0);

                    ReminderLog::create([
                        'reminder_id' => $rule->id,
                        'order_id' => $order->id,
                        'customer_id' => $order->customer_id,
                        'scheduled_at' => $scheduledAt,
                        'status' => 'pending',
                        'message_sent' => false,
                    ]);
                }
            });
        }

        // Fetch updated logs
        $reminders = ReminderLog::with(['customer', 'order', 'reminder'])
            ->orderBy('scheduled_at', 'asc')
            ->paginate(10);

        // Return partial view
        return view('pages.reminder.partials.logs_table', compact('reminders'))->render();
    }
}
