<?php

namespace Monet\Framework\Module\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Monet\Framework\Module\Facades\Modules;
use Monet\Framework\Module\Installer\ModuleInstaller;
use Monet\Framework\Module\Installer\ModuleInstallerInterface;
use Monet\Framework\Module\Loader\ModuleLoader;
use Monet\Framework\Module\Loader\ModuleLoaderInterface;
use Monet\Framework\Module\Models\Module;
use Monet\Framework\Module\Policies\ModulePolicy;
use Monet\Framework\Module\Policies\ThemePolicy;
use Monet\Framework\Module\Repository\ModuleRepository;
use Monet\Framework\Module\Repository\ModuleRepositoryInterface;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ModuleLoaderInterface::class,
            ModuleLoader::class
        );

        $this->app->singleton(
            ModuleInstallerInterface::class,
            ModuleInstaller::class
        );

        $this->app->alias(
            ModuleRepositoryInterface::class,
            'monet.modules'
        );
        $this->app->singleton(
            ModuleRepositoryInterface::class,
            ModuleRepository::class
        );

        $this->app->booting(function () {
            Modules::boot();
        });
    }

    public function boot(): void
    {
        Gate::policy(Module::class, ModulePolicy::class);
    }

    public function provides(): array
    {
        return [
            ModuleLoaderInterface::class,
            ModuleLoader::class,
            ModuleInstallerInterface::class,
            ModuleInstaller::class,
            ModuleRepositoryInterface::class,
            ModuleRepository::class,
        ];
    }
}
