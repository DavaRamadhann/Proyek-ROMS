<?php

namespace App\Domains\Customer\Repositories;

use App\Domains\Customer\Interfaces\CustomerRepositoryInterface;
use App\Domains\Customer\Models\Customer;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function findByPhone(string $phone): ?Customer
    {
        return Customer::where('phone', $phone)->first();
    }

    public function create(array $data): Customer
    {
        // Pastikan nama tidak null
        return Customer::create([
            'name'  => $data['name'] ?? 'Guest ' . substr($data['phone'], -4),
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
        ]);
    }

    // [BARU] Tambahkan fungsi ini agar ChatService tidak error
    public function update(int $id, array $data): bool
    {
        $customer = Customer::find($id);
        if ($customer) {
            return $customer->update($data);
        }
        return false;
    }
}