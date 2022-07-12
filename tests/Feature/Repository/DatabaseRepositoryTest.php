<?php

declare(strict_types=1);

namespace Dbus\Tests\Feature\Repository;

use Carbon\Carbon;
use Dbus\Tests\Feature\FeatureTestCase;
use Dbus\Tests\Support\Database\DbusDatabase;
use Dbus\Tests\Support\Mock\Repository\DbusDatabaseRepository;
use Dbus\Tests\Support\Mock\Scope\Database\DbusEmailDatabaseScope;
use Dbus\Tests\Support\Mock\Scope\Database\DbusRoleDatabaseScope;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Query\Builder;

final class DatabaseRepositoryTest extends FeatureTestCase
{
    private ?DbusDatabaseRepository $repository;

    private ?Cache $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $repository = $this->app->make(DbusDatabaseRepository::class);

        if ($repository instanceof DbusDatabaseRepository) {
            $this->repository = $repository;

        }
        $this->cache = $this->app->make(Cache::class);
    }

    public function testRepositoryRetrievesCorrectResultsWithoutScopes(): void
    {
        $this->assertEmpty($this->repository->getBuilder()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getBuilder()->get());

        DbusDatabase::createManager();
        $this->assertCount(2, $this->repository->getBuilder()->get());
    }

    public function testRepositoryRetrievesCorrectResultsWithScopes(): void
    {
        $this->assertEmpty($this->repository->getBuilder()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getBuilder()->get());

        $this->repository->withBuilderScope(DbusRoleDatabaseScope::onlyManager());
        $this->assertEmpty($this->repository->getBuilder()->get());

        DbusDatabase::createManager();
        $this->assertCount(1, $this->repository->getBuilder()->get());
    }

    public function testRepositoryScopesCanBeCombined(): void
    {
        $this->assertEmpty($this->repository->getBuilder()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getBuilder()->get());

        $this->repository->withBuilderScope(DbusRoleDatabaseScope::onlyManager());
        $this->assertEmpty($this->repository->getBuilder()->get());

        DbusDatabase::createManager();
        $this->assertCount(1, $this->repository->getBuilder()->get());

        $this->repository->withBuilderScope(
            DbusEmailDatabaseScope::forEmail(DbusDatabase::USER_ADMIN_EMAIL)
        );
        $this->assertEmpty($this->repository->getBuilder()->get());
    }

    public function testRepositoryBuilderCanSkipScopesApply(): void
    {
        $this->assertEmpty($this->repository->getBuilder()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getBuilder()->get());

        $this->repository->withBuilderScope(DbusRoleDatabaseScope::onlyManager());

        // with scope for manager role
        $this->assertEmpty($this->repository->getBuilder()->get());

        // without scope for manager role
        $this->assertCount(1, $this->repository->getBuilder(false)->get());
    }

    public function testQueryCanBeCached(): void
    {
        $expectedAdminCount = 1;
        $cacheKey = 'admin_count';
        DbusDatabase::createAdmin();

        $this->repository->cache(
            $cacheKey,
            Carbon::now()->addWeek(),
            static fn(Builder $query) => $query
                    ->where(
                        DbusDatabase::USERS_ROLE_COLUMN_NAME,
                        '=',
                        DbusDatabase::USER_ROLE_ADMIN
                    )
                    ->count(DbusDatabase::USERS_ID_COLUMN_NAME)
        );

        $this->assertTrue($this->cache->has($cacheKey));
        $this->assertSame($expectedAdminCount, $this->cache->get($cacheKey));
    }

    public function testQueryCanBeCachedWithDatabaseScopes(): void
    {
        $expectedManagerCount = 1;
        $cacheKey = 'manager_count';

        DbusDatabase::createAdmin();
        DbusDatabase::createManager();

        $this->repository
            ->withBuilderScope(DbusRoleDatabaseScope::onlyManager())
            ->cache(
                $cacheKey,
                Carbon::now()->addWeek(),
                static fn(Builder $query) => $query->count(DbusDatabase::USERS_ID_COLUMN_NAME)
            );

        $this->assertTrue($this->cache->has($cacheKey));
        $this->assertSame($expectedManagerCount, $this->cache->get($cacheKey));
    }

    public function testQueryCacheMethodCanSkipDatabaseScopes(): void
    {
        $expectedUsersCount = 2;
        $cacheKey = 'users_count';

        DbusDatabase::createAdmin();
        DbusDatabase::createManager();

        $this->repository
            ->withBuilderScope(DbusRoleDatabaseScope::onlyManager())
            ->cache(
                $cacheKey,
                Carbon::now()->addWeek(),
                static fn(Builder $query) => $query->count(DbusDatabase::USERS_ID_COLUMN_NAME),
                false
            );

        $this->assertTrue($this->cache->has($cacheKey));
        $this->assertSame($expectedUsersCount, $this->cache->get($cacheKey));
    }
}
