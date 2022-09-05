<?php

namespace Monet\Framework\Settings\Drivers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class SettingsFileDriver extends SettingsDriverBase
{
    protected bool $booted = false;

    public function get(string $key, $default = null)
    {
        return Arr::get($this->data, $key, function () use ($key, $default) {
            $this->data = $this->boot($key, $default);

            return Arr::get($this->data, $key, $default);
        });
    }

    public function save(): void
    {
        if (empty($this->updated) && empty($this->deleted)) {
            return;
        }

        $storage = $this->getStorage();

        $path = $this->getPath();

        $storage->put($path, $this->encode($this->data));

        $this->setCache('file', $this->data);
    }

    protected function load(string $key)
    {
        $storage = $this->getStorage();

        $path = $this->getPath();

        if (! $storage->exists($path)) {
            return [];
        }

        return $this->decode($storage->get($path));
    }

    protected function boot(string $key, $default)
    {
        // If boot has been called twice then the setting doesn't exist
        // so let's just return the data
        if ($this->booted) {
            return $this->data;
        }

        $this->booted = true;

        if (! ($value = $this->loadCache($key))) {
            $value = $this->load($key) ?? [];
        }

        return $value;
    }

    protected function getStorage(): Filesystem
    {
        return Storage::disk(config('monet.settings.file.disk'));
    }

    protected function getPath(): string
    {
        return config('monet.settings.file.path');
    }

    protected function getCacheKey(string $key): string
    {
        return config('monet.settings.cache.key');
    }
}
