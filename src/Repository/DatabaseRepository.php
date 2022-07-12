<?php

declare(strict_types=1);

namespace Dbus\Repository;

use DateTimeInterface;
use Dbus\Repository\Contract\Scope\DatabaseScope;
use Dbus\Repository\Exception\DatabaseRepositoryException;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;

abstract class DatabaseRepository implements Contract\DatabaseRepository, Contract\CacheableRepository
{
    /** @var array<DatabaseScope> */
    protected array $builderScopes = [];

    protected ?ConnectionInterface $connection = null;

    protected ?Cache $cache = null;

    public function setConnection(ConnectionInterface $connection): void
    {
        $this->connection = $connection;
    }

    public function getConnection(): ConnectionInterface
    {
        if ($this->connection === null) {
            throw DatabaseRepositoryException::connectionIsNotSet($this);
        }

        return $this->connection;
    }

    public function setCache(Cache $cache): Contract\CacheableRepository
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache(): Cache
    {
        if ($this->cache === null) {
            throw DatabaseRepositoryException::cacheIsNotSet($this);
        }

        return $this->cache;
    }

    public function getBuilder(bool $withScopes = true): Builder
    {
        $builder = $this->getConnection()->table($this->getTable());

        if ($withScopes) {
            return $this->applyBuilderScopes($builder);
        }

        return $builder;
    }

    /** @param array<DatabaseScope> $scopes */
    public function setBuilderScopes(array $scopes): self
    {
        $this->builderScopes = array_filter(
            $scopes,
            static fn($scope) => $scope instanceof DatabaseScope
        );

        return $this;
    }

    /** @return array<DatabaseScope> */
    public function getBuilderScopes(): array
    {
        return $this->builderScopes;
    }

    public function withBuilderScope(DatabaseScope $scope): self
    {
        $this->builderScopes[$scope->getIdentifier()] = $scope;

        return $this;
    }

    /** @return mixed */
    public function cache(string $key, DateTimeInterface $ttl, callable $callback, bool $withScopes = true)
    {
        $builder = $this->getBuilder($withScopes);

        return $this
            ->getCache()
            ->remember($key, $ttl, static fn() => $callback($builder));
    }

    protected function applyBuilderScopes(Builder $builder): Builder
    {
        foreach ($this->builderScopes as $scope) {
            $builder->beforeQuery($scope->toClosure());
        }

        return $builder;
    }
}
