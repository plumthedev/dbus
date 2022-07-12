<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Context\Unit;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Mockery;

trait BuilderContext
{
    public function mockQueryBuilder(): QueryBuilder
    {
        return Mockery::mock(QueryBuilder::class)->makePartial();
    }

    public function mockEloquentBuilder(): EloquentBuilder
    {
        return Mockery::mock(EloquentBuilder::class)->makePartial();
    }
}
