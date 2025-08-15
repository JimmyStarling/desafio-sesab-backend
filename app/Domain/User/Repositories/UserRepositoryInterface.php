<?php

namespace App\Domain\User\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator;
    public function create(bool $is_authenticated, array $data, array $address = []): User; //$addressIds
    public function update(User $user, array $data, $address = []): User; //$addressIds
    public function delete(User $user): bool;
}
