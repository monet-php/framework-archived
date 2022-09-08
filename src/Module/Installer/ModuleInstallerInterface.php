<?php

namespace Monet\Framework\Module\Installer;

use Monet\Framework\Installer\Component\ComponentInstallerInterface;

interface ModuleInstallerInterface extends ComponentInstallerInterface
{
    public function install(string $path, ?string &$reason = null): ?string;
}
