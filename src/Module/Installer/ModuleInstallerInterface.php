<?php

namespace Monet\Framework\Module\Installer;

interface ModuleInstallerInterface
{
    public function install(string $path, ?string &$reason = null): ?string;

    public function publish(array $providers): void;
}
