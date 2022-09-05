<?php

namespace Monet\Framework\Module\Exception;

use Exception;
use Monet\Framework\Module\Module;

class ModuleNotFoundException extends Exception
{
    public static function module(string|Module $module): static
    {
        $name = $module;
        if ($name instanceof Module) {
            $name = $name->getName();
        }

        return new(sprintf('Cannot find module "%s"', $name));
    }
}
