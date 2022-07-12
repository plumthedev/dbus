<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Mock\Model;

use Dbus\Tests\Support\Database\DbusDatabase;
use Illuminate\Database\Eloquent\Model;

final class DbusModel extends Model
{
    public function getTable(): string
    {
        return DbusDatabase::USERS_TABLE_NAME;
    }
}
