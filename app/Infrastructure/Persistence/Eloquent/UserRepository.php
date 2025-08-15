<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Models\Address;
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

    public function create(bool $is_authenticated, array $data, array $address = []): User
    {
        // Validate required fields
        $profileId = $this->userService->checkProfile(
            auth()->check(),
            $data['profile_id']
        );
        if (!isset($data['name'], $data['email'], $data['cpf'], $data['password'], $data['profile_id'])) {
            throw new \InvalidArgumentException('Required fields are missing.');
        }
        if ($data['profile_id'] === 0) {
            throw new \Exception('Unauthorized to create user with this profile.', 403);
        }
        
        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'password' => Hash::make($data['password']),
            'profile_id' => $data['profile_id'],
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        $address = $data['address'] ?? [];

        // Sync addresses
        $this->syncAddresses($user, $address);
        
        // Load address and profile
        return $user->load('profile', 'address');
    }

    public function update(User $user, array $data, $address = []): User
    {
        $user->update($data);

        $this->syncAddresses($user, $address);

        return $user->load('profile', 'address');
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
