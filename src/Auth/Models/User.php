<?php

namespace Monet\Framework\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Monet\Framework\Auth\Contracts\ShouldVerifyEmail;
use Monet\Framework\Support\Traits\Macroable;
use Monet\Framework\Transformer\Facades\Transformer;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements ShouldVerifyEmail
{
    use Macroable;
    use HasRoles;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->macroConstruct();
    }

    public function getFillable(): array
    {
        return Transformer::transform(
            'monet.auth.user.model.fillable',
            [
                'name',
                'email',
                'password',
                'email_verified_at',
            ]
        );
    }

    public function getHidden(): array
    {
        return Transformer::transform(
            'monet.auth.user.model.hidden',
            [
                'password',
                'remember_token',
            ]
        );
    }

    public function getCasts(): array
    {
        return Transformer::transform(
            'monet.auth.user.model.casts',
            [
                'email_verified_at' => 'datetime',
            ]
        );
    }

    public function shouldVerifyEmail(): bool
    {
        return Transformer::transform(
            'monet.auth.user.model.shouldVerifyEmail',
            config('monet.auth.require_email_verification')
        );
    }
}
