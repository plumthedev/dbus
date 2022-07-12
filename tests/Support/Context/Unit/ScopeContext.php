<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Context\Unit;

use Dbus\Repository\Scope\DatabaseScope;
use Dbus\Repository\Scope\EloquentScope;
use Dbus\Repository\Scope\Scope;
use Mockery;

trait ScopeContext
{
    protected function mockScope(): Scope
    {
        return Mockery::mock(Scope::class)->makePartial();
    }

    protected function mockDatabaseScope(bool $withApplyCall = false): DatabaseScope
    {
        $mock = Mockery::mock(DatabaseScope::class);

        if ($withApplyCall) {
            $mock->expects('apply')->once();
        }

        return $mock->makePartial();
    }

    protected function mockQueryScope(bool $withApplyCall = false): EloquentScope
    {
        $mock = Mockery::mock(EloquentScope::class);

        if ($withApplyCall) {
            $mock->expects('apply')->once();
        }

        return $mock->makePartial();
    }
}
