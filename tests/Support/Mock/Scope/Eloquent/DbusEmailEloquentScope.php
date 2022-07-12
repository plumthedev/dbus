<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Mock\Scope\Eloquent;

use Dbus\Repository\Scope\EloquentScope;
use Dbus\Tests\Support\Database\DbusDatabase;
use Illuminate\Database\Eloquent\Builder;

final class DbusEmailEloquentScope extends EloquentScope
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
