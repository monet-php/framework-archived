<?php

namespace Monet\Framework\Admin\Providers;

use Filament\PluginServiceProvider;
use Monet\Framework\Admin\Filament\Resources\ModuleResource;
use Monet\Framework\Admin\Filament\Resources\ThemeResource;

class AdminServiceProvider extends PluginServiceProvider
{
    public static string $name = 'monet-admin';

    protected array $resources = [
        ModuleResource::class,
        ThemeResource::class
    ];
}
