<?php

namespace App\Domains\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Order\Models\Order;
use App\Domains\Order\Models\OrderItem;
use App\Domains\Customer\Models\Customer;
use App\Domains\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')->latest()->paginate(10);
        return view('pages.order.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('pages.order.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }

            // Create Order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_id' => $request->customer_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Create Order Items
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items']);
        return view('pages.order.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,shipped,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;

        if ($request->status == 'shipped' && !$order->shipped_at) {
            $order->shipped_at = now();
        }

        $order->save();

        // Trigger Reminder Creation if status changed to completed
        if ($request->status == 'completed' && $oldStatus != 'completed') {
            $this->scheduleReminder($order);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    protected function scheduleReminder(Order $order)
    {
        // Cari rule reminder yang cocok (misal berdasarkan produk pertama atau default)
        // Untuk MVP: Ambil rule pertama yang aktif atau buat default
        $reminderRule = \App\Domains\Reminder\Models\Reminder::active()->first();

        if (!$reminderRule) {
            // Buat default rule jika belum ada
            $reminderRule = \App\Domains\Reminder\Models\Reminder::create([
                'name' => 'Pengingat Standar',
                'days_after_delivery' => 30, // Default 30 hari
                'message_template' => "Halo {name}, sudah 30 hari sejak pesanan kopi Anda. Apakah stok sudah menipis? Yuk pesan lagi di Someah!",
                'is_active' => true,
            ]);
        }

        // Hitung tanggal kirim
        $scheduledAt = now()->addDays($reminderRule->days_after_delivery);

        // Cek apakah sudah ada log untuk order ini agar tidak duplikat
        $exists = \App\Domains\Reminder\Models\ReminderLog::where('order_id', $order->id)->exists();

        if (!$exists) {
            \App\Domains\Reminder\Models\ReminderLog::create([
                'reminder_id' => $reminderRule->id,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
            ]);
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();
            
            // Items will be deleted automatically if cascade is set, 
            // but let's be safe and delete them explicitly or rely on DB constraint.
            // Assuming cascade on delete in migration, but if not:
            // $order->items()->delete();
            
            $order->delete();
            
            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
        }
    }
}
