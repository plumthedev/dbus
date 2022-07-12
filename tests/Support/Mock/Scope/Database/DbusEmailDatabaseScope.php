<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Mock\Scope\Database;

use Dbus\Repository\Scope\DatabaseScope;
use Dbus\Tests\Support\Database\DbusDatabase;
use Illuminate\Database\Query\Builder;

final class DbusEmailDatabaseScope extends DatabaseScope
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function forEmail(string $email): self
    {
        return new self($email);
    }

    public function apply(Builder $query): void
    {
        $query->where(DbusDatabase::USERS_EMAIL_COLUMN_NAME, '=', $this->email);
    }
}
