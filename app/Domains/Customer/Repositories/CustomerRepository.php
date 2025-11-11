<?php
// app/Domains/Customer/Repositories/CustomerRepository.php

namespace App\Domains\Customer\Repositories;

use App\Domains\Customer\Interfaces\CustomerRepositoryInterface;
use App\Domains\Customer\Models\Customer;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function findByPhone(string $phone): ?Customer
    {
        // TODO: Normalisasi nomor HP (e.g., 0812 -> 62812)
        // Untuk sekarang, kita cari apa adanya
        return Customer::where('phone', $phone)->first();
    }

    public function create(array $data): Customer
    {
        // Hanya membuat data minimal yang diperlukan dari chat
        return Customer::create([
            'name'  => $data['name'] ?? 'Guest ' . substr($data['phone'], -4),
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
        ]);
    }
}