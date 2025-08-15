<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Auth\Repositories\AuthRepositoryInterface;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Domain\User\Services\UserService;
use Illuminate\Support\Str;

class AuthRepository implements AuthRepositoryInterface
{

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(bool $is_authenticated, array $data): User
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
        $this->userService->syncAddresses($user, $address);
        
        // Load address and profile
        return $user->load('profile', 'address');
    }

    public function login(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    public function getUserProfile(User $user): User
    {
        return $user->load('address');
    }
}
