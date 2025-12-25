<?php

namespace App\Domains\Chat\Repositories;

use App\Domains\Chat\Interfaces\ChatContactRepositoryInterface;
use App\Domains\Chat\Models\ChatContact;

class ChatContactRepository implements ChatContactRepositoryInterface
{
    public function findByPhone(string $phone): ?ChatContact
    {
        return ChatContact::where('phone', $phone)->first();
    }

    public function create(array $data): ChatContact
    {
        return ChatContact::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $contact = ChatContact::find($id);
        if ($contact) {
            return $contact->update($data);
        }
        return false;
    }
}
