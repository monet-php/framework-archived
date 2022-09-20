<?php

namespace Monet\Framework\Admin\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Monet\Framework\Admin\Filament\Resources\UserResource;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function afterCreate(): void
    {
        $this->record->assignRole(Role::findById(1));
    }
}
