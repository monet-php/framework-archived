<?php

namespace Monet\Framework\Support\Traits;

use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

trait Macroable
{
    protected static array $macros = [];

    protected static array $extensionCallbacks = [];

    public function macroConstruct(): void
    {
        foreach (static::$extensionCallbacks as $callback) {
            call_user_func($callback->bindTo($this, static::class), $this);
        }
    }

    public static function macro(string $name, object|callable $macro): void
    {
        static::$macros[$name] = $macro;
    }

    public static function extend(callable $callback): void
    {
        static::$extensionCallbacks[] = $callback;
    }

    public static function mixin(object|string $mixin): void
    {
        $class = new ReflectionClass($mixin);

        $methods = $class->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            $method->setAccessible(true);
            static::macro($method->name, $method->invoke($mixin));
        }

        $properties = $class->getProperties(
            ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED
        );

        foreach ($properties as $property) {
            $property->setAccessible(true);
            static::extend(function () use ($class, $property) {
                $this->{$property->getName()} = $property->getValue($class);
            });
        }
    }

    public static function hasExtension(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    public static function __callStatic($method, $parameters)
    {
        if (! static::hasExtension($method)) {
            $parent = get_parent_class();
            if ($parent && method_exists($parent, '__callStatic')) {
                return parent::__callStatic($method, $parameters);
            }

            throw new BadMethodCallException(
                sprintf(
                    'Method "%s" does not exist.',
                    $method
                )
            );
        }

        $extension = static::$macros[$method];

        if ($extension instanceof Closure) {
            return call_user_func_array(
                Closure::bind($extension, null, static::class),
                $parameters
            );
        }

        return call_user_func_array($extension, $parameters);
    }

    public function __call($method, $parameters)
    {
        if (! static::hasExtension($method)) {
            $parent = get_parent_class();
            if ($parent && method_exists($parent, '__call')) {
                return parent::__call($method, $parameters);
            }

            throw new BadMethodCallException(
                sprintf(
                    'Method "%s" does not exist.',
                    $method
                )
            );
        }

        $extension = static::$macros[$method];

        if ($extension instanceof Closure) {
            return call_user_func_array(
                $extension->bindTo($this, static::class),
                $parameters
            );
        }

        return call_user_func_array($extension, $parameters);
    }
}
