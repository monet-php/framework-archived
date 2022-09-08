<?php

namespace Monet\Framework\Auth\Providers;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Monet\Framework\Auth\Http\Livewire\EmailVerification;
use Monet\Framework\Auth\Http\Livewire\Login;
use Monet\Framework\Auth\Http\Livewire\PasswordConfirmation;
use Monet\Framework\Auth\Http\Livewire\PasswordRequest;
use Monet\Framework\Auth\Http\Livewire\PasswordReset;
use Monet\Framework\Auth\Http\Livewire\Register;
use Monet\Framework\Auth\Http\Responses\LogoutResponse;
use Monet\Framework\Auth\Models\User;
use Monet\Framework\Auth\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LogoutResponseContract::class,
            LogoutResponse::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../../routes/auth.php');

        $this->registerLivewireComponents();

        Gate::policy(User::class, UserPolicy::class);
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('monet::auth.login', Login::class);
        Livewire::component('monet::auth.register', Register::class);
        Livewire::component('monet::auth.password-reset', PasswordReset::class);
        Livewire::component('monet::auth.password-request', PasswordRequest::class);
        Livewire::component('monet::auth.password-confirmation', PasswordConfirmation::class);
        Livewire::component('monet::auth.email-verification', EmailVerification::class);
    }
}
