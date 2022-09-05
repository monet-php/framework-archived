<?php

namespace Monet\Framework\Theme\Loader;

use Monet\Framework\Support\Json;
use Monet\Framework\Theme\Theme;

class ThemeLoader implements ThemeLoaderInterface
{
    public function fromPath(string $path): Theme
    {
        $fullPath = realpath($path.'/composer.json');

        $json = Json::make($fullPath);

        return Theme::make(
            $json->get('name'),
            $json->get('description'),
            $path,
            $json->get('extra.monet.theme.parent'),
            $json->get('extra.monet.theme.providers', []),
            $json->get('extra.monet.theme.dependencies', []),
        );
    }

    public function fromArray(array $array): Theme
    {
        return Theme::make(
            $array['name'],
            $array['description'],
            $array['path'],
            $array['parent'],
            $array['providers'],
            $array['dependencies']
        );
    }
}
