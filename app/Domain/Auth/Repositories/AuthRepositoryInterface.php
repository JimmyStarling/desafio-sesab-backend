<?php

namespace App\Domain\Auth\Repositories;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function register(array $data): User;

    public function login(string $email, string $password): ?User;

    public function logout(User $user): void;

    public function getUserProfile(User $user): User;
}
