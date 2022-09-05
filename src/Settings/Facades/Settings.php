<?php

namespace Monet\Framework\Settings\Facades;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'monet.settings';
    }
}
