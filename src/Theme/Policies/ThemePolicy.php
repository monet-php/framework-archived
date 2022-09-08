<?php

namespace Monet\Framework\Theme\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Monet\Framework\Auth\Models\User;
use Monet\Framework\Support\Traits\Macroable;
use Monet\Framework\Theme\Models\Theme;

class ThemePolicy
{
    use Macroable, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Theme $theme): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Theme $theme): bool
    {
        return $user->hasPermissionTo('install themes');
    }

    public function delete(User $user, Theme $theme): bool
    {
        return $user->hasPermissionTo('delete themes');
    }

    public function restore(User $user, Theme $theme): bool
    {
        return false;
    }

    public function forceDelete(User $user, Theme $theme): bool
    {
        return $this->delete($user, $theme);
    }
}
