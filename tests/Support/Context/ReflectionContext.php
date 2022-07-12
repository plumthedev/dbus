<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Context;

use ReflectionProperty;

trait ReflectionContext
{
    protected function reflectProperty(
        object $instance,
        string $propertyName,
        bool $accessible = true
    ): ReflectionProperty {
        $property = new ReflectionProperty($instance, $propertyName);
        $property->setAccessible($accessible);

        return $property;
    }
}
