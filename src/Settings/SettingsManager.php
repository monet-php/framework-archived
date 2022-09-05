<?php

namespace Monet\Framework\Settings;

use Illuminate\Support\Manager;
use Monet\Framework\Settings\Drivers\SettingsDatabaseDriver;
use Monet\Framework\Settings\Drivers\SettingsFileDriver;

class SettingsManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('monet.settings.driver');
    }

    public function createFileDriver(): SettingsFileDriver
    {
        return $this->container->make(SettingsFileDriver::class);
    }

    public function createDatabaseDriver(): SettingsDatabaseDriver
    {
        return $this->container->make(SettingsDatabaseDriver::class);
    }
}
