<?php

namespace Dbus\Examples\Eloquent;

use Dbus\Examples\Database\Scope\SimpleDatabaseScope;
use Dbus\Examples\Eloquent\Model\EloquentModel;
use Dbus\Examples\Eloquent\Scope\SimpleEloquentScope;
use Dbus\Repository\EloquentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EloquentModelRepository extends EloquentRepository
{
    private EloquentModel $model;

    public function __construct(EloquentModel $model)
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        // return new EloquentModel();
        // return EloquentModel::make();
        // you should return model instance here, as you wish

        return $this->model;
    }

    public function simpleQueryByModel(): Collection
    {
        // will return collection of model
        return $this
            ->getQuery()
            ->get();
    }

    public function simpleQueryByDatabase(): Collection
    {
        // will return collection of StdObjects
        return $this
            ->getBuilder()
            ->get();
    }

    public function queryWithNonReusableScope(string $uuid): Collection
    {
        return $this
            ->getQuery()
            ->where('uuid', '=', $uuid)
            ->get();
    }

    public function queryWithReusableScope(): Collection
    {
        return $this
            ->withQueryScope(SimpleEloquentScope::create())
            ->getQuery()
            ->get();
    }

    public function queryWithReusableScopeForEloquentAndDatabase(): Collection
    {
        return $this
            ->withBuilderScope(SimpleDatabaseScope::create())
            ->withQueryScope(SimpleEloquentScope::create())
            //->getBuilder() // only database scopes will be applied
            ->getQuery() // both scopes will be applied
            ->get();
    }
}