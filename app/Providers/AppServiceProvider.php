<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\User\Repositories\UserRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\UserRepository::class
        );
      
        $this->app->bind(
            \App\Domain\Auth\Repositories\AuthRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\AuthRepository::class
        );

        $this->app->bind(
            \App\Domain\Address\Repositories\AddressRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\AddressRepository::class
        );

        $this->app->bind(UserService::class, function ($app) {
            return new UserService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
