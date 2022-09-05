<?php

namespace Monet\Framework\Tests;

use Illuminate\Support\Facades\Storage;
use Monet\Framework\Auth\Models\User;
use Monet\Framework\MonetServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    protected function getPackageProviders($app): array
    {
        return [
            MonetServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config([
            'database.default' => 'testing',
            'auth.providers.users.model' => User::class,
        ]);
    }
}
