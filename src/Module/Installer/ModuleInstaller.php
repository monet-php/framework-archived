<?php

namespace Monet\Framework\Module\Installer;

use Exception;
use Illuminate\Support\Arr;
use Monet\Framework\Installer\Component\ComponentInstaller;
use Monet\Framework\Module\Facades\Modules;

class ModuleInstaller extends ComponentInstaller implements ModuleInstallerInterface
{
    public function install(string $path, ?string &$reason = null): ?string
    {
        if (! ($zip = $this->getArchive($path))) {
            $reason = 'Failed to find uploaded module';

            return null;
        }

        if (! ($index = $this->findManifestIndex($zip))) {
            $reason = 'Failed to find module manifest';

            return null;
        }

        if (! ($manifest = $this->getManifest($zip, $index))) {
            $reason = 'Failed to get module manifest';

            return null;
        }

        if (! $this->validate($manifest)) {
            $reason = 'Module manifest is invalid';

            return null;
        }

        $name = $manifest['name'];

        if (Modules::find($name) !== null) {
            $reason = 'Module is already installed';

            return null;
        }

        $paths = config('monet.modules.paths');
        if (empty($paths)) {
            $reason = 'No configured module installation paths';

            return null;
        }

        if (! $this->extract($zip, $name, Arr::first($paths))) {
            $reason = 'Failed to extract module';

            return null;
        }

        return $name;
    }

    protected function validate(array $manifest): bool
    {
        try {
            if (! isset($manifest['name'])) {
                return false;
            }

            if (! isset($manifest['extra'])) {
                return false;
            }

            if (! isset($manifest['extra']['monet'])) {
                return false;
            }

            return isset($manifest['extra']['monet']['module']);
        } catch (Exception) {
            return false;
        }
    }
}
