<?php

declare(strict_types=1);

namespace Dbus\Repository\Contract;

use Dbus\Repository\Contract\Scope\DatabaseScope;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;

interface DatabaseRepository
{
    public function getTable(): string;

    public function getBuilder(bool $withScopes = true): Builder;

    public function setConnection(ConnectionInterface $connection): void;

    public function getConnection(): ConnectionInterface;

    public function withBuilderScope(DatabaseScope $scope): self;

    /** @param array<DatabaseScope> $scopes */
    public function setBuilderScopes(array $scopes): self;

    /** @return array<DatabaseScope> */
    public function getBuilderScopes(): array;
}
