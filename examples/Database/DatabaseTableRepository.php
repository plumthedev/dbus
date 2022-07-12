<?php

namespace Dbus\Examples\Database;

use Carbon\Carbon;
use Dbus\Examples\Database\Scope\SimpleDatabaseScope;
use Dbus\Repository\DatabaseRepository;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class DatabaseTableRepository extends DatabaseRepository
{
    public function getTable(): string
    {
        return 'users';
    }

    public function simpleQueryWithoutScopes(array $attributes): Collection
    {
        return $this
            ->getBuilder()
            ->get();
    }

    public function queryWithDryConditions(string $uuid): Collection
    {
        return $this
            ->getBuilder()
            ->where('uuid', '=', $uuid)
            ->get();
    }

    public function queryWithReusableScope(): Collection
    {
        return $this
            ->withBuilderScope(SimpleDatabaseScope::create())
            ->getBuilder()
            ->get();
    }

    public function queryWithCacheAndWithoutScopes(): Collection
    {
        return $this
            ->cache('cache_key', Carbon::now()->addDay(), function (Builder $query) {
                // query instance does not have any scopes applied
                return $query->get();
            }, false);
    }

    public function queryWithCacheAndScopes(): Collection
    {
        return $this
            ->withBuilderScope(SimpleDatabaseScope::create())
            ->cache('cache_key', Carbon::now()->addWeek(), function (Builder $query) {
                // scopes are applied to query instance
                return $query->get();
            });
    }
}