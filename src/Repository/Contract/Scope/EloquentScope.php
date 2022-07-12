<?php

declare(strict_types=1);

namespace Dbus\Repository\Contract\Scope;

use Illuminate\Database\Eloquent\Builder;

interface EloquentScope extends Scope
{
    public function apply(Builder $query): void;
}
