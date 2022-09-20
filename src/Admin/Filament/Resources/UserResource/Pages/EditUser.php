<?php

namespace Monet\Framework\Admin\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Monet\Framework\Admin\Filament\Resources\UserResource;
use Monet\Framework\Auth\Models\User;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
}
