<?php

use Monet\Framework\Settings\Facades\Settings;

it('returns the default value if setting does not exist when getting', function () {
    expect(Settings::driver('file')->get('::setting-key::', '::default-value::'))
        ->toEqual('::default-value::');
});

it('can set a value', function () {
    Settings::driver('file')
        ->put('::setting-key::', '::setting-value::');

    expect(Settings::driver('file')->get('::setting-key::'))
        ->toEqual('::setting-value::');
});

it('can get the updated value', function () {
    Settings::driver('file')
        ->put('::setting-key::', '::setting-value::');

    expect(Settings::driver('file')->get('::setting-key::'))
        ->toEqual('::setting-value::');

    Settings::driver('file')
        ->put('::setting-key::', '::updated-setting-value::');

    expect(Settings::driver('file')->get('::setting-key::'))
        ->toEqual('::updated-setting-value::');
});

it('can delete settings', function () {
    Settings::driver('file')
        ->put('::setting-key::', '::setting-value::');

    expect(Settings::driver('file')->get('::setting-key::'))
        ->toEqual('::setting-value::');

    Settings::driver('file')
        ->forget('::setting-key::');

    expect(Settings::driver('file')->get('::setting-key::'))
        ->toBeNull();
});

it('can get then delete settings', function () {
    Settings::driver('file')
        ->put('::setting-key::', '::setting-value::');

    expect(Settings::driver('file')->pull('::setting-key::'))
        ->toEqual('::setting-value::');
    expect(Settings::driver('file')->get('::setting-key::'))
        ->toBeNull();
});

it('returns the default value if setting does not exist when pulling', function () {
    expect(Settings::driver('file')->pull('::setting-key::', '::default-value::'))
        ->toEqual('::default-value::');
});
