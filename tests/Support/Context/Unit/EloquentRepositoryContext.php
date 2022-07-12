<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Context\Unit;

use Dbus\Repository\EloquentRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Mockery;

trait EloquentRepositoryContext
{
    private function mockEloquentModel(string $table): Model
    {
        return Mockery::mock(Model::class, [
            'getTable' => $table,
            'getConnection' => Mockery::mock(ConnectionInterface::class, [
                'table' => Mockery::mock(Builder::class)->makePartial(),
            ]),
        ])->makePartial();
    }

    private function mockEloquentRepository(?Model $model = null): EloquentRepository
    {
        return Mockery::mock(EloquentRepository::class, [
            'getModel' => $model ?? $this->mockEloquentModel('mocked'),
        ])->makePartial();
    }
}
