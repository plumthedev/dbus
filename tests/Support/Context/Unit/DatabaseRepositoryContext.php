<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Context\Unit;

use Dbus\Repository\DatabaseRepository;
use Illuminate\Database\Connection;
use Mockery;

trait DatabaseRepositoryContext
{
    public function mockDatabaseRepository(string $table): DatabaseRepository
    {
        return Mockery::mock(DatabaseRepository::class, ['getTable' => $table])->makePartial();
    }

    public function mockConnection(): Connection
    {
        return Mockery::mock(Connection::class)->makePartial();
    }
}
