<?php

namespace Dbus\Examples\Database\Scope;

use Dbus\Repository\Scope\DatabaseScope;
use Illuminate\Database\Query\Builder;

class SimpleDatabaseScope extends DatabaseScope
{
    public static function create(): SimpleDatabaseScope
    {
        return new self();
    }

    public function apply(Builder $query): void
    {
        $query->where('column_name', '=', 'column_value');
    }
}