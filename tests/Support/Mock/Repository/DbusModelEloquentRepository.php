<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Mock\Repository;

use Dbus\Repository\EloquentRepository;
use Dbus\Tests\Support\Mock\Model\DbusModel;
use Illuminate\Database\Eloquent\Model;

final class DbusModelEloquentRepository extends EloquentRepository
{
    public function getModel(): Model
    {
        return new DbusModel();
    }
}
