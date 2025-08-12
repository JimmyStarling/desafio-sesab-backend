<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use Illuminate\Support\Collection;

class AddressRepository implements AddressRepositoryInterface
{
    public function all(): Collection
    {
        return Address::with('users')->get();
    }

    public function find(int $id): ?Address
    {
        return Address::with('users')->find($id);
    }

    public function create(array $data, array $users = []): Address
    {
        $address = Address::create($data);

        if (!empty($users)) {
            $address->users()->sync($users);
        }

        return $address->load('users');
    }

    public function update(Address $address, array $data, array $users = []): Address
    {
        $address->update($data);

        if (!empty($users) || array_key_exists('users', $data)) {
            $address->users()->sync($users ?? []);
        }

        return $address->load('users');
    }

    public function delete(Address $address): bool
    {
        $address->users()->detach();
        return $address->delete();
    }
}