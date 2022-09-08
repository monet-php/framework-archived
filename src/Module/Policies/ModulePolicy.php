<?php

namespace Monet\Framework\Module\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Monet\Framework\Auth\Models\User;
use Monet\Framework\Module\Models\Module;
use Monet\Framework\Support\Traits\Macroable;

class ModulePolicy
{
    use Macroable, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('update modules');
    }

    public function view(User $user, Module $module): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Module $module): bool
    {
        return $user->hasPermissionTo('update modules');
    }

    public function delete(User $user, Module $module): bool
    {
        return $user->hasPermissionTo('delete modules');
    }

    public function restore(User $user, Module $module): bool
    {
        return false;
    }

    public function forceDelete(User $user, Module $module): bool
    {
        return $this->delete($user, $module);
    }
}
