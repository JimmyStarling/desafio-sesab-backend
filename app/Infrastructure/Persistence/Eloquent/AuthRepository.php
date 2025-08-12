<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Auth\Repositories\AuthRepositoryInterface;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(array $data): User
    {
        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'password' => Hash::make($data['password']),
            'profile_id' => $data['profile_id'],
        ]);

        $addressIds = [];

        foreach ($data['address'] as $addressData) {
            $address = Address::create($addressData);
            $addressIds[] = $address->id;
        }

        if (!empty($addressIds)) {
            $user->address()->sync($addressIds);
        } else {
            throw new \Exception("User must have at least one address.");
        }

        // Load the address relationship
        return $user->load('address');
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
