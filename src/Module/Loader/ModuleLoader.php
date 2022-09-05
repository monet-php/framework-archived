<?php

namespace Monet\Framework\Module\Loader;

use Monet\Framework\Module\Module;
use Monet\Framework\Support\Json;

class ModuleLoader implements ModuleLoaderInterface
{
    public function fromPath(string $path): Module
    {
        $fullPath = realpath($path.'/composer.json');

        $json = Json::make($fullPath);

        return Module::make(
            $json->get('name'),
            $json->get('description'),
            $json->get('version'),
            $path,
            'disabled',
            $json->get('extra.monet.module.dependencies', []),
            $json->get('extra.monet.module.providers', [])
        );
    }

    public function fromArray(array $array): Module
    {
        return Module::make(
            $array['name'],
            $array['description'],
            $array['version'],
            $array['path'],
            $array['status'],
            $array['dependencies'],
            $array['providers']
        );
    }
}
