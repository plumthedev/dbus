<?php

declare(strict_types=1);

namespace Dbus\Repository\Contract\Scope;

use Closure;

interface Scope
{
    public function getIdentifier(): string;

    public function toClosure(): Closure;
}
