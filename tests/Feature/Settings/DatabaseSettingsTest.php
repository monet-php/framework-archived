<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Monet\Framework\Settings\Facades\Settings;
use Monet\Framework\Settings\Models\Setting;

beforeEach(function () {
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();
        $table->json('value');
        $table->boolean('autoload')->default(true)->index();
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::drop('settings');
});

it('can read from database', function () {
    Setting::query()
        ->create([
            'key' => '::setting-key::',
            'value' => '::setting-value::',
        ]);

    expect(Settings::driver('database')->get('::setting-key::'))
        ->toEqual('::setting-value::');
});

it('returns the default value if setting does not exist when getting', function () {
    expect(Settings::driver('database')->get('::setting-key::', '::default-value::'))
        ->toEqual('::default-value::');
});

it('can set a value', function () {
    Settings::driver('database')
        ->put('::setting-key::', '::setting-value::');

    expect(Settings::driver('database')->get('::setting-key::'))
        ->toEqual('::setting-value::');
});

it('can get the updated value', function () {
    Settings::driver('database')
        ->put('::setting-key::', '::setting-value::');

    expect(Settings::driver('database')->get('::setting-key::'))
        ->toEqual('::setting-value::');

    Settings::driver('database')
        ->put('::setting-key::', '::updated-setting-value::');

    expect(Settings::driver('database')->get('::setting-key::'))
        ->toEqual('::updated-setting-value::');
});

it('can delete settings', function () {
    Settings::driver('database')
        ->put('::setting-key::', '::setting-value::');

    expect(Settings::driver('database')->get('::setting-key::'))
        ->toEqual('::setting-value::');

    Settings::driver('database')
        ->forget('::setting-key::');

    expect(Settings::driver('database')->get('::setting-key::'))
        ->toBeNull();
});

it('can get then delete settings', function () {
    Settings::driver('database')
        ->put('::setting-key::', '::setting-value::');

    expect(Settings::driver('database')->pull('::setting-key::'))
        ->toEqual('::setting-value::');
    expect(Settings::driver('database')->get('::setting-key::'))
        ->toBeNull();
});

it('returns the default value if setting does not exist when pulling', function () {
    expect(Settings::driver('database')->pull('::setting-key::', '::default-value::'))
        ->toEqual('::default-value::');
});
