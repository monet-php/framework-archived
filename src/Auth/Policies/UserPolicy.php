<?php

namespace Monet\Framework\Auth\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Monet\Framework\Auth\Models\User;
use Monet\Framework\Support\Traits\Macroable;

class UserPolicy
{
    use Macroable, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('update users');
    }

    public function view(User $user, User $model): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create users');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('update users');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->getAuthIdentifier() !== $model->getAuthIdentifier() &&
            $user->hasPermissionTo('delete users');
    }

    public function restore(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }
}
