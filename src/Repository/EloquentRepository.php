<?php

declare(strict_types=1);

namespace Dbus\Repository;

use DateTimeInterface;
use Dbus\Repository\Contract\Scope\EloquentScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentRepository extends DatabaseRepository implements Contract\EloquentRepository
{
    /** @var array<EloquentScope> */
    protected array $queryScopes = [];

    public function getTable(): string
    {
        return $this->getModel()->getTable();
    }

    public function getQuery(bool $withScopes = true): Builder
    {
        $model = $this->getModel();

        $this->setupRepositoryFromModel($model);
        $query = $this->setupQueryFromModel($model, $withScopes);

        if ($withScopes) {
            return $this->applyQueryScopes($query);
        }

        return $query->withoutGlobalScopes();
    }

    /** @return array<EloquentScope> */
    public function getQueryScopes(): array
    {
        return $this->queryScopes;
    }

    /** @param array<EloquentScope> $scopes */
    public function setQueryScopes(array $scopes): self
    {
        $this->queryScopes = array_filter(
            $scopes,
            static fn($item) => $item instanceof EloquentScope
        );

        return $this;
    }

    public function withQueryScope(EloquentScope $scope): self
    {
        $this->queryScopes[$scope->getIdentifier()] = $scope;

        return $this;
    }

    /** @return mixed */
    public function cache(string $key, DateTimeInterface $ttl, callable $callback, bool $withScopes = true)
    {
        $builder = $this->getQuery($withScopes);

        return $this
            ->getCache()
            ->remember($key, $ttl, static fn() => $callback($builder));
    }

    protected function applyQueryScopes(Builder $query): Builder
    {
        foreach ($this->queryScopes as $scope) {
            $query->withGlobalScope($scope->getIdentifier(), $scope->toClosure());
        }

        return $query->applyScopes();
    }

    protected function setupRepositoryFromModel(Model $model): void
    {
        $this->setConnection($model->getConnection());
    }

    protected function setupQueryFromModel(Model $model, bool $withScopes): Builder
    {
        $builder = $this->getBuilder($withScopes);
        $builder->connection = $model->getConnection();
        $builder->from = $model->getTable();

        $query = new Builder($builder);
        $query->setModel($model);
        $query->connection = $model->getConnection();
        $query->from = $model->getTable();

        return $query;
    }
}
