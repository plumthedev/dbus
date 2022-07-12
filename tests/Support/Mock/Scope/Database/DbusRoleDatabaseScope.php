<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Mock\Scope\Database;

use Dbus\Repository\Scope\DatabaseScope;
use Dbus\Tests\Support\Database\DbusDatabase;
use Illuminate\Database\Query\Builder;

final class DbusRoleDatabaseScope extends DatabaseScope
{
    private string $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public static function onlyManager(): DbusRoleDatabaseScope
    {
        return new self(DbusDatabase::USER_ROLE_MANAGER);
    }

    public function apply(Builder $query): void
    {
        $query->where(DbusDatabase::USERS_ROLE_COLUMN_NAME, '=', $this->role);
    }
}
