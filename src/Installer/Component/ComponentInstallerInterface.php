<?php

namespace Monet\Framework\Installer\Component;

interface ComponentInstallerInterface
{
    public function publish(array $providers, bool $migrate = true): void;
}
