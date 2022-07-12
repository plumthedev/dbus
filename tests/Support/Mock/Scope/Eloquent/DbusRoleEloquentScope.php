<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Mock\Scope\Eloquent;

use Dbus\Repository\Scope\EloquentScope;
use Dbus\Tests\Support\Database\DbusDatabase;
use Illuminate\Database\Eloquent\Builder;

final class DbusRoleEloquentScope extends EloquentScope
{
    private string $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public static function onlyAdmin(): self
    {
        return new self(DbusDatabase::USER_ROLE_ADMIN);
    }

    public function apply(Builder $query): void
    {
        $query->where(DbusDatabase::USERS_ROLE_COLUMN_NAME, '=', $this->role);
    }
}
