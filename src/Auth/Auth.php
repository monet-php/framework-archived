<?php

namespace Monet\Framework\Auth;

use Filament\Facades\Filament;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;
use Monet\Framework\Auth\Models\User;
use Monet\Framework\Support\Traits\Macroable;
use Spatie\Permission\Models\Role;

class Auth
{
    use Macroable;

    public function login(
        array $credentials,
        bool $updatePasswordConfirmed = true
    ): void {
        $authQuery = [
            function ($query) {
                $query->orWhere('email', '=', 'email');
            },
        ];

        $usernameEnabled = config('monet.auth.allow_username_login');

        if ($usernameEnabled) {
            $usernameIdentifier = User::getUsernameIdentifierName();

            $username = $credentials[$usernameIdentifier] ?? $credentials['email'];

            $authQuery = [
                function ($query) use ($usernameIdentifier, $username) {
                    $query->orWhere('email', '=', 'email')
                        ->orWhere($usernameIdentifier, '=', $username);
                },
            ];
        }

        $remember = $credentials['remember'] ?? false;

        if (! Filament::auth()->attempt($authQuery, $remember)) {
            $validationErrorKey = $usernameEnabled ?
                'monet.auth.errors.emailOrUsername' :
                'monet.auth.errors.email';

            throw ValidationException::withMessages([
                'email' => __($validationErrorKey),
            ]);
        }

        if ($updatePasswordConfirmed) {
            session()->put('auth.password_confirmed_at', time());
        }
    }

    public static function register(
        array $attributes,
        bool $loginAfter = true,
        bool $silently = false
    ): User {
        $user = User::query()
            ->create($attributes);

        $user->assignRole(Role::findById(1));

        if (! $silently) {
            event(new Registered($user));
        }

        if ($loginAfter) {
            Filament::auth()->login($user);
        }

        return $user;
    }
}
