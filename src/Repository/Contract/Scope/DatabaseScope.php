<?php

declare(strict_types=1);

namespace Dbus\Repository\Contract\Scope;

use Illuminate\Database\Query\Builder;

interface DatabaseScope extends Scope
{
    public function apply(Builder $query): void;
}
