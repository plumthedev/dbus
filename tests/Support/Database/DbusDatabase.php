<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Database;

use Illuminate\Support\Facades\DB;

final class DbusDatabase
{
    public const USERS_TABLE_NAME = 'users';
    public const USERS_ID_COLUMN_NAME = 'id';
    public const USERS_EMAIL_COLUMN_NAME = 'email';
    public const USERS_ROLE_COLUMN_NAME = 'role';
    public const USER_ADMIN_EMAIL = 'admin@example.com';
    public const USER_MANAGER_EMAIL = 'manager@example.com';
    public const USER_ROLE_ADMIN = 'admin';
    public const USER_ROLE_MANAGER = 'manager';

    public static function createAdmin(): void
    {
        self::createUser(self::USER_ADMIN_EMAIL, self::USER_ROLE_ADMIN);
    }

    public static function createManager(): void
    {
        self::createUser(self::USER_MANAGER_EMAIL, self::USER_ROLE_MANAGER);
    }

    private static function createUser(string $email, string $role): void
    {
        DB::table(self::USERS_TABLE_NAME)->insert([
            self::USERS_EMAIL_COLUMN_NAME => $email,
            self::USERS_ROLE_COLUMN_NAME => $role,
        ]);
    }
}
