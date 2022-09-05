<?php

namespace Monet\Framework\Settings\Drivers;

use Illuminate\Support\Arr;
use Monet\Framework\Settings\Models\Setting;

class SettingsDatabaseDriver extends SettingsDriverBase
{
    protected bool $booted = false;

    public function save(): void
    {
        ['key' => $keyColumn, 'value' => $valueColumn] = $this->getColumns();

        if (! empty($this->updated)) {
            Setting::query()
                ->upsert(
                    collect($this->updated)
                        ->map(fn (string $key): array => [
                            $keyColumn => $key,
                            $valueColumn => $this->encode($this->get($key)),
                        ])
                        ->all(),
                    $keyColumn
                );

            foreach ($this->updated as $key) {
                $this->setCache($key, $this->get($key));
            }
        }

        if (! empty($this->deleted)) {
            Setting::query()
                ->whereIn($keyColumn, $this->deleted)
                ->delete();

            foreach ($this->deleted as $key) {
                $this->clearCache($key);
            }
        }
    }

    protected function load(string $key)
    {
        [
            'key' => $keyColumn,
            'value' => $valueColumn,
            'autoload' => $autoloadColumn
        ] = $this->getColumns();

        if (! $this->booted) {
            $this->data = Setting::query()
                ->where($autoloadColumn, '=', true)
                ->get([$keyColumn, $valueColumn])
                ->mapWithKeys(fn (Setting $setting): array => [
                    $setting->{$keyColumn} => $setting->{$valueColumn},
                ])
                ->all();

            $this->booted = true;

            if (($value = Arr::get($this->data, $key))) {
                return $value;
            }
        }

        return Setting::query()
            ->where($keyColumn, '=', $key)
            ->first($valueColumn)
            ?->value;
    }

    protected function getColumns(): array
    {
        return config('monet.settings.database.columns');
    }
}
