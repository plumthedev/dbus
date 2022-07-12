<?php

namespace Dbus\Examples\Eloquent\Scope;

use Dbus\Repository\Scope\EloquentScope;
use Illuminate\Database\Eloquent\Builder;

class SimpleEloquentScope extends EloquentScope
{
    public static function create(): SimpleEloquentScope
    {
        return new self();
    }

    public function apply(Builder $query): void
    {
        // here you can join, query, apply wheres, and many many more

        $query
            ->join('joined', 'joined.foreign_key', '=', 'model.primary_key')
            ->where('joined.foreign_key', '=', 'value');
    }
}