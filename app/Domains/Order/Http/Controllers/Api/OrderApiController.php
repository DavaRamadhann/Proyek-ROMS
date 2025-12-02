<?php

namespace App\Domains\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Order\Models\Order;
use App\Domains\Order\Models\OrderItem;
use App\Domains\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderApiController extends Controller
{
    /**
     * Create a new order via API
     * Endpoint: POST /api/v1/orders
     */
    public function store(Request $request)
    {
        // 0. Security Check (Simple API Key)
        $apiKey = $request->header('X-API-KEY');
        $validKey = env('API_KEY', 'someah-secret-key');

        if ($apiKey !== $validKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid API Key'
            ], 401);
        }

        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'customer' => 'required|array',
            'customer.phone' => 'required|string', // Kunci utama identifikasi
            'customer.name' => 'nullable|string',
            'customer.email' => 'nullable|email',
            'customer.address' => 'nullable|string',
            
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            
            'external_id' => 'nullable|string', // ID dari sistem asal (Shopify/dll)
            'notes' => 'nullable|string',
            'created_at' => 'nullable|date', // Support backdate order
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 2. Find or Create Customer
            $phone = $this->normalizePhone($request->input('customer.phone'));
            
            $customer = Customer::firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => $request->input('customer.name') ?? 'Guest ' . substr($phone, -4),
                    'email' => $request->input('customer.email'),
                    'address' => $request->input('customer.address'),
                ]
            );

            // Update info customer jika ada data baru yang lebih lengkap (opsional)
            if ($request->has('customer.name') && $customer->name !== $request->input('customer.name')) {
                $customer->update(['name' => $request->input('customer.name')]);
            }

            // 3. Hitung Total
            $totalAmount = 0;
            foreach ($request->input('items') as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }

            // 4. Create Order
            $orderDate = $request->input('created_at') ? \Carbon\Carbon::parse($request->input('created_at')) : now();
            
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_id' => $customer->id,
                'total_amount' => $totalAmount,
                'status' => 'pending', // Default status
                'notes' => $request->input('notes'),
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // 5. Create Order Items
            foreach ($request->input('items') as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            // Trigger Event OrderCreated untuk Otomasi
            event(new \App\Domains\Order\Events\OrderCreated($order));

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $customer->id
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Order Create Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Helper: Normalize Phone Number to 62xxx
     */
    private function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
