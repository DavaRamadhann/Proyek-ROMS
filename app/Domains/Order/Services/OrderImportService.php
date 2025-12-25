<?php

namespace App\Domains\Order\Services;

use App\Domains\Order\Models\Order;
use App\Domains\Order\Models\OrderItem;
use App\Domains\Customer\Models\Customer;
use App\Domains\Product\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrderImportService
{
    public function import(string $filePath)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("Gagal membuka file CSV.");
        }

        // Baca header
        $header = fgetcsv($handle, 1000, ',');
        
        // Normalisasi header (lowercase, trim)
        $header = array_map(function($h) {
            return strtolower(trim($h));
        }, $header);

        // Validasi header minimal
        $requiredHeaders = ['customer_name', 'customer_phone', 'product_name', 'quantity'];
        $missingHeaders = array_diff($requiredHeaders, $header);
        
        if (!empty($missingHeaders)) {
            fclose($handle);
            throw new \Exception("Format CSV salah. Header yang hilang: " . implode(', ', $missingHeaders));
        }

        DB::beginTransaction();
        try {
            $rowNumber = 1;
            $ordersBuffer = []; // Kelompokkan berdasarkan order_number (jika ada)

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $rowNumber++;
                
                // Skip baris kosong
                if (empty(array_filter($data))) continue;

                $row = array_combine($header, $data);
                
                // Ambil data
                $orderNumber = $row['order_number'] ?? null;
                $customerName = trim($row['customer_name']);
                $customerPhone = trim($row['customer_phone']);
                $productName = trim($row['product_name']);
                $qty = (int) $row['quantity'];
                $notes = $row['notes'] ?? '';
                $paymentMethod = $row['payment_method'] ?? 'manual';

                if (empty($customerName) || empty($customerPhone) || empty($productName) || $qty <= 0) {
                    throw new \Exception("Data tidak lengkap di baris ke-$rowNumber.");
                }

                // Jika order_number kosong, generate unik sementara untuk grouping diproses ini
                if (empty($orderNumber)) {
                    $orderNumber = 'AUTO-CTX-' . $rowNumber; // Sementara, nanti diganti real order number per group
                }

                // Grouping
                if (!isset($ordersBuffer[$orderNumber])) {
                    $ordersBuffer[$orderNumber] = [
                        'customer_name' => $customerName,
                        'customer_phone' => $customerPhone,
                        'payment_method' => $paymentMethod,
                        'notes' => $notes,
                        'items' => []
                    ];
                }

                $ordersBuffer[$orderNumber]['items'][] = [
                    'product_name' => $productName,
                    'quantity' => $qty
                ];
            }

            fclose($handle);

            // Proses setiap Order yang sudah di-buffer
            foreach ($ordersBuffer as $tempOrderNumber => $data) {
                $this->processSingleOrder($data);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            throw $e;
        }
    }

    private function processSingleOrder($data)
    {
        // 1. Find or Create Customer
        // Normalisasi phone number (hapus karakter non-digit, pastikan awalan 62/0)
        // Di sini kita asumsi user input raw.
        // Sebaiknya ada helper untuk sanitize phone. Kita pakai simple dulu.
        
        $phone = preg_replace('/[^0-9]/', '', $data['customer_phone']);
        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $customer = Customer::firstOrCreate(
            ['phone' => $phone],
            ['name' => $data['customer_name']]
        );

        // 2. Prepare Items & Calculate Total
        $totalAmount = 0;
        $orderItemsData = [];

        foreach ($data['items'] as $itemData) {
            // Find Product (Fuzzy Search - Case Insensitive)
            $product = Product::where(DB::raw('LOWER(name)'), strtolower($itemData['product_name']))->first();

            if (!$product) {
                // Opsi: Skip atau Throw? Throw lebih aman agar user sadar typo.
                throw new \Exception("Produk tidak ditemukan: " . $itemData['product_name']);
            }

            // Cek Stok
            if ($product->stock < $itemData['quantity']) {
                throw new \Exception("Stok tidak cukup untuk produk: " . $product->name);
            }

            $subtotal = $product->price * $itemData['quantity'];
            $totalAmount += $subtotal;

            // Kurangi Stok
            $product->decrement('stock', $itemData['quantity']);

            $orderItemsData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $itemData['quantity'],
                'price' => $product->price,
                'subtotal' => $subtotal
            ];
        }

        // 3. Create Order
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(10)), // Generate Real Order Number
            'customer_id' => $customer->id,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_method' => $data['payment_method'],
            'notes' => $data['notes'],
        ]);

        // 4. Create Order Items
        foreach ($orderItemsData as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }
    }
}
