<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Database;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

trait SetupDatabase
{
    protected function setupDatabase(Application $app): void
    {
        $this->setupDatabaseEnvironment($app['config']);
        $this->setupDatabaseSchema($app['db']->connection());
    }

    public function setupDatabaseEnvironment(Repository $config): void
    {
        $config->set('database.default', 'sqlite');
        $config->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function setupDatabaseSchema(Connection $connection): void
    {
        $connection->getSchemaBuilder()->create(
            DbusDatabase::USERS_TABLE_NAME,
            static function (Blueprint $table): void {
                $table->unsignedBigInteger(DbusDatabase::USERS_ID_COLUMN_NAME)->primary();
                $table->string(DbusDatabase::USERS_EMAIL_COLUMN_NAME)->unique();
                $table->string(DbusDatabase::USERS_ROLE_COLUMN_NAME);
            }
        );
    }
}
