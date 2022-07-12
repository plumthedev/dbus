<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Mock\Repository;

use Dbus\Repository\DatabaseRepository;
use Dbus\Tests\Support\Database\DbusDatabase;

final class DbusDatabaseRepository extends DatabaseRepository
{
    public function getTable(): string
    {
        return DbusDatabase::USERS_TABLE_NAME;
    }
}
