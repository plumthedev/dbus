<?php

declare(strict_types=1);

namespace Dbus\Repository\Scope;

use Closure;
use Dbus\Repository\Contract;

abstract class DatabaseScope extends Scope implements Contract\Scope\DatabaseScope
{
    public function toClosure(): Closure
    {
        return Closure::fromCallable([$this, 'apply']);
    }
}
