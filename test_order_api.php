<?php

// Script sederhana untuk mengetes API Create Order
// Jalankan dengan: php test_order_api.php

$url = 'http://127.0.0.1:8000/api/v1/orders';

$data = [
    'customer' => [
        'phone' => '081234567890',
        'name' => 'Budi Tester',
        'email' => 'budi@example.com',
        'address' => 'Jl. Test No. 123, Jakarta'
    ],
    'items' => [
        [
            'product_name' => 'Kopi Arabika 200g',
            'quantity' => 2,
            'price' => 75000
        ],
        [
            'product_name' => 'Gula Aren Sachet',
            'quantity' => 1,
            'price' => 15000
        ]
    ],
    'notes' => 'Tolong dikirim segera ya',
    'external_id' => 'SHOPIFY-1001'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

echo "Mengirim request ke $url...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n$response\n";
