<?php

namespace Monet\Framework\Admin\Providers;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Monet\Framework\Admin\Filament\Pages\SiteSettings;
use Monet\Framework\Admin\Filament\Resources\ModuleResource;
use Monet\Framework\Admin\Filament\Resources\ThemeResource;
use Monet\Framework\Admin\Filament\Resources\UserResource;

class AdminServiceProvider extends PluginServiceProvider
{
    public static string $name = 'monet-admin';

    protected array $pages = [
        SiteSettings::class,
    ];

    protected array $resources = [
        ModuleResource::class,
        ThemeResource::class,
        UserResource::class
    ];

    public function packageBooted(): void
    {
        parent::packageBooted();

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                'Users',
                'Appearance',
                'Extend',
                'Administration',
            ]);

            Filament::registerTheme(
                mix('css/monet.css', 'monet'),
            );
        });
    }
}
