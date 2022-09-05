<?php

namespace Monet\Framework\Theme\Providers;

use Illuminate\Support\ServiceProvider;
use Monet\Framework\Theme\Facades\Themes;
use Monet\Framework\Theme\Installer\ThemeInstaller;
use Monet\Framework\Theme\Installer\ThemeInstallerInterface;
use Monet\Framework\Theme\Loader\ThemeLoader;
use Monet\Framework\Theme\Loader\ThemeLoaderInterface;
use Monet\Framework\Theme\Repository\ThemeRepository;
use Monet\Framework\Theme\Repository\ThemeRepositoryInterface;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ThemeLoaderInterface::class,
            ThemeLoader::class
        );

        $this->app->singleton(
            ThemeInstallerInterface::class,
            ThemeInstaller::class
        );

        $this->app->alias(
            ThemeRepositoryInterface::class,
            'monet.themes'
        );
        $this->app->singleton(
            ThemeRepositoryInterface::class,
            ThemeRepository::class
        );

        $this->app->booting(function () {
            Themes::boot();
        });
    }

    public function provides(): array
    {
        return [
            ThemeLoaderInterface::class,
            ThemeLoader::class,
            ThemeInstallerInterface::class,
            ThemeInstaller::class,
            ThemeRepositoryInterface::class,
            ThemeRepository::class,
        ];
    }
}
