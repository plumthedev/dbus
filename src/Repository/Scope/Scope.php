<?php

declare(strict_types=1);

namespace Dbus\Repository\Scope;

use Dbus\Repository\Contract;

abstract class Scope implements Contract\Scope\Scope
{
    protected string $id = '';

    public function getIdentifier(): string
    {
        return $this->id ?: static::class;
    }
}
