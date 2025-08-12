<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = User::with(['profile', 'address']);

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['cpf'])) {
            $query->where('cpf', $filters['cpf']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                $filters['start_date'],
                $filters['end_date']
            ]);
        }

        return $query->paginate(10);
    }

    public function create(array $data, array $addressIds = []): User
    {
        $user = User::create($data);

        if (!empty($addressIds)) {
            $user->address()->sync($addressIds);
        }

        return $user->load('profile', 'address');
    }

    public function update(User $user, array $data, array $addressIds = []): User
    {
        $user->update($data);

        if (!empty($addressIds)) {
            $user->address()->sync($addressIds);
        }

        return $user->load('profile', 'address');
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}