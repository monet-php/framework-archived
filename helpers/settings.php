<?php

if (! function_exists('settings')) {
    function settings(?string $key = null, $default = null)
    {
        $settings = app('monet.settings');

        return empty($key) ? $settings : $settings->get($key, $default);
    }
}

if (! function_exists('settings_get')) {
    function settings_get(string $key, $default = null)
    {
        return settings($key, $default);
    }
}

if (! function_exists('settings_pull')) {
    function settings_pull(string $key, $default = null)
    {
        return settings()->pull($key, $default);
    }
}

if (! function_exists('settings_put')) {
    function settings_put(string $key, $value): void
    {
        settings()->put($key, $value);
    }
}

if (! function_exists('settings_forget')) {
    function settings_forget(string $key): void
    {
        settings()->forget($key);
    }
}
