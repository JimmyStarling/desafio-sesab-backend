<?php

namespace App\Domain\User\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator;
    public function create(array $data, array $addressIds = []): User;
    public function update(User $user, array $data, array $addressIds = []): User;
    public function delete(User $user): bool;
}
