<?php

namespace App\Domain\Address\Repositories;

use Illuminate\Support\Collection;
use App\Models\Address;

interface AddressRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Address;
    public function create(array $data, array $users = []): Address;
    public function update(Address $address, array $data, array $users = []): Address;
    public function delete(Address $address): bool;
}