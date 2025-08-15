<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('create-user-with-profile', function ($user, $profileId) {
            if ($user->profile_id == 1) return true; // admin pode tudo
            if ($user->profile_id == 2 && in_array($profileId, [2, 3])) return true; // gerente cria até gerente
            return $profileId == 3; // padrão só cria padrão
        });
    }
}
