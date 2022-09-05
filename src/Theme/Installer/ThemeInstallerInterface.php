<?php

namespace Monet\Framework\Theme\Installer;

interface ThemeInstallerInterface
{
    public function install(string $path, ?string &$reason = null): ?string;

    public function publish(array $providers): void;
}
