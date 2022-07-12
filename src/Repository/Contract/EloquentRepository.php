<?php

declare(strict_types=1);

namespace Dbus\Repository\Contract;

use Dbus\Repository\Contract\Scope\EloquentScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepository extends DatabaseRepository
{
    public function getModel(): Model;

    public function getQuery(bool $withScopes = true): Builder;

    public function withQueryScope(EloquentScope $scope): self;

    /** @param array<EloquentScope> $scopes */
    public function setQueryScopes(array $scopes): self;

    /** @return array<EloquentScope> */
    public function getQueryScopes(): array;
}
