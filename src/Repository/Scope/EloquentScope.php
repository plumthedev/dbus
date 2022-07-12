<?php

declare(strict_types=1);

namespace Dbus\Repository\Scope;

use Closure;
use Dbus\Repository\Contract;

abstract class EloquentScope extends Scope implements Contract\Scope\EloquentScope
{
    public function toClosure(): Closure
    {
        return Closure::fromCallable([$this, 'apply']);
    }
}
