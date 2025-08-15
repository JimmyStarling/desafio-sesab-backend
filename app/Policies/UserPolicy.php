<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function create(User $authUser)
    {
        // Apenas admins e gestores podem criar usuários de outros perfis
        return in_array($authUser->profile_id, [1, 2]); // 1 = Admin, 2 = Gestor
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->profile_id === 1;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $authUser): bool
    {
        return $authUser->profile_id === 1;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->profile_id === 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authUser, User $targetUser): bool
    {
        return $authUser->profile_id === 1;
    }
}
