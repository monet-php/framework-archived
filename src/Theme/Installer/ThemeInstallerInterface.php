<?php

namespace Monet\Framework\Theme\Installer;

use Monet\Framework\Installer\Component\ComponentInstallerInterface;

interface ThemeInstallerInterface extends ComponentInstallerInterface
{
    public function install(string $path, ?string &$reason = null): ?string;
}
