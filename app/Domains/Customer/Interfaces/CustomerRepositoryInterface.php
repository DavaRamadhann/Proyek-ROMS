<?php
// app/Domains/Customer/Interfaces/CustomerRepositoryInterface.php

namespace App\Domains\Customer\Interfaces;

use App\Domains\Customer\Models\Customer;

interface CustomerRepositoryInterface
{
    /**
     * Cari customer berdasarkan nomor HP.
     * Kritis untuk service chat.
     *
     * @param string $phone
     * @return Customer|null
     */
    public function findByPhone(string $phone): ?Customer;

    /**
     * Buat customer baru jika tidak ditemukan.
     *
     * @param array $data
     * @return Customer
     */
    public function create(array $data): Customer;
}