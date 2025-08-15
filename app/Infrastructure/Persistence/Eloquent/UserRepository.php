<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Models\Address;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Domain\User\Services\UserService;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

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

    public function create(bool $is_authenticated, array $usersData): array
    {
        $createdUsers = [];

        foreach ($usersData as $data) {
            // Valida campos obrigatórios
            if (!isset($data['name'], $data['email'], $data['cpf'], $data['password'], $data['profile_id'])) {
                throw new \InvalidArgumentException('Required fields are missing.');
            }

            // Checa profile_id via serviço
            $profileId = $this->userService->checkProfile(
                $is_authenticated,
                $data['profile_id']
            );

            if ($profileId === 0) {
                throw new \Exception('Unauthorized to create user with this profile.', 403);
            }

            // Criação do usuário
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf' => $data['cpf'],
                'password' => bcrypt($data['password']),
                'profile_id' => $profileId,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);

            // Sincroniza endereços
            $addresses = $data['address'] ?? [];
            $this->userService->syncAddresses($user, $addresses);

            // Carrega relacionamento
            $createdUsers[] = $user->load('profile', 'address');
        }

        return $createdUsers;
    }

    public function update(User $user, array $data, $address = []): User
    {
        $user->update($data);

        $this->userService->syncAddresses($user, $address);

        return $user->load('profile', 'address');
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
