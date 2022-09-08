<?php

namespace Monet\Framework\Settings\Providers;

use Illuminate\Support\ServiceProvider;
use Monet\Framework\Settings\Console\Commands\SettingsTableCommand;
use Monet\Framework\Settings\SettingsManager;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->alias(
            SettingsManager::class,
            'monet.settings'
        );
        $this->app->singleton(SettingsManager::class);

        $this->app->terminating(function () {
            $this->app->make('monet.settings')->save();
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SettingsTableCommand::class,
            ]);
        }
    }

    public function provides(): array
    {
        return [
            SettingsManager::class,
        ];
    }
}
