<?php

namespace App\Domains\Chat\Interfaces;

use App\Domains\Chat\Models\ChatContact;

interface ChatContactRepositoryInterface
{
    public function findByPhone(string $phone): ?ChatContact;
    public function create(array $data): ChatContact;
    public function update(int $id, array $data): bool;
}
