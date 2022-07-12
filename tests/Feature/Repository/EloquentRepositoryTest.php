<?php

declare(strict_types=1);

namespace Dbus\Tests\Feature\Repository;

use Carbon\Carbon;
use Dbus\Tests\Feature\FeatureTestCase;
use Dbus\Tests\Support\Database\DbusDatabase;
use Dbus\Tests\Support\Mock\Repository\DbusModelEloquentRepository;
use Dbus\Tests\Support\Mock\Scope\Database\DbusEmailDatabaseScope;
use Dbus\Tests\Support\Mock\Scope\Eloquent\DbusEmailEloquentScope;
use Dbus\Tests\Support\Mock\Scope\Eloquent\DbusRoleEloquentScope;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Builder;

final class EloquentRepositoryTest extends FeatureTestCase
{
    private ?DbusModelEloquentRepository $repository;

    private ?Cache $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(DbusModelEloquentRepository::class);
        $this->cache = $this->app->make(Cache::class);
    }

    public function testRepositoryRetrievesCorrectResultsWithoutScopes(): void
    {
        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getQuery()->get());

        DbusDatabase::createManager();
        $this->assertCount(2, $this->repository->getQuery()->get());
    }

    public function testRepositoryRetrievesCorrectResultsWithScopes(): void
    {
        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createManager();
        $this->assertCount(1, $this->repository->getQuery()->get());

        $this->repository->withQueryScope(DbusRoleEloquentScope::onlyAdmin());

        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getQuery()->get());
    }

    public function testRepositoryScopesCanBeCombined(): void
    {
        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createManager();
        $this->assertCount(1, $this->repository->getQuery()->get());

        $this->repository->withQueryScope(DbusRoleEloquentScope::onlyAdmin());
        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getQuery()->get());

        $this->repository->withQueryScope(
            DbusEmailEloquentScope::forEmail(DbusDatabase::USER_MANAGER_EMAIL)
        );
        $this->assertEmpty($this->repository->getQuery()->get());
    }

    public function testRepositoryBuilderScopesAndEloquentScopesCanBeCombined(): void
    {
        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createManager();
        $this->assertCount(1, $this->repository->getQuery()->get());

        $this->repository->withQueryScope(DbusRoleEloquentScope::onlyAdmin());
        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getQuery()->get());

        $this->repository->withBuilderScope(
            DbusEmailDatabaseScope::forEmail(DbusDatabase::USER_MANAGER_EMAIL)
        );
        $this->assertEmpty($this->repository->getQuery()->get());
    }

    public function testRepositoryScopesCanBeSkipped(): void
    {
        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createManager();
        $this->assertCount(1, $this->repository->getQuery()->get());

        $this->repository->withQueryScope(DbusRoleEloquentScope::onlyAdmin());

        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getQuery()->get());

        // lets skip scopes
        $this->assertCount(2, $this->repository->getQuery(false)->get());
    }

    public function testRepositorySkipQueryScopesAndBuilderScopes(): void
    {
        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createManager();
        $this->assertCount(1, $this->repository->getQuery()->get());

        $this->repository->withQueryScope(DbusRoleEloquentScope::onlyAdmin());
        $this->assertEmpty($this->repository->getQuery()->get());

        DbusDatabase::createAdmin();
        $this->assertCount(1, $this->repository->getQuery()->get());

        $this->repository->withBuilderScope(
            DbusEmailDatabaseScope::forEmail(DbusDatabase::USER_MANAGER_EMAIL)
        );
        $this->assertEmpty($this->repository->getQuery()->get());

        // lets skip scopes
        $this->assertCount(2, $this->repository->getQuery(false)->get());
    }

    public function testQueryCanBeCachedWithQueryScopes(): void
    {
        $expectedAdminCount = 1;
        $cacheKey = 'admin_count';

        DbusDatabase::createAdmin();
        DbusDatabase::createManager();

        $this->repository
            ->withQueryScope(DbusRoleEloquentScope::onlyAdmin())
            ->cache(
                $cacheKey,
                Carbon::now()->addWeek(),
                static fn(Builder $query) => $query->count(DbusDatabase::USERS_ID_COLUMN_NAME)
            );

        $this->assertTrue($this->cache->has($cacheKey));
        $this->assertSame($expectedAdminCount, $this->cache->get($cacheKey));
    }

    public function testQueryCanBeCachedWithQueryAndDatabaseScopes(): void
    {
        $expectedAdminCount = 0;
        $cacheKey = 'admin_count_with_manager_email';

        DbusDatabase::createAdmin();
        DbusDatabase::createManager();

        $this->repository
            ->withBuilderScope(DbusEmailDatabaseScope::forEmail(DbusDatabase::USER_MANAGER_EMAIL))
            ->withQueryScope(DbusRoleEloquentScope::onlyAdmin())
            ->cache(
                $cacheKey,
                Carbon::now()->addWeek(),
                static fn(Builder $query) => $query->count(DbusDatabase::USERS_ID_COLUMN_NAME)
            );

        $this->assertTrue($this->cache->has($cacheKey));
        $this->assertSame($expectedAdminCount, $this->cache->get($cacheKey));
    }

    public function testQueryCacheMethodCanSkipQueryScopes(): void
    {
        $expectedAdminCount = 2;
        $cacheKey = 'admin_count_with_manager_email';

        DbusDatabase::createAdmin();
        DbusDatabase::createManager();

        $this->repository
            ->withBuilderScope(DbusEmailDatabaseScope::forEmail(DbusDatabase::USER_MANAGER_EMAIL))
            ->withQueryScope(DbusRoleEloquentScope::onlyAdmin())
            ->cache(
                $cacheKey,
                Carbon::now()->addWeek(),
                static fn(Builder $query) => $query->count(DbusDatabase::USERS_ID_COLUMN_NAME),
                false
            );

        $this->assertTrue($this->cache->has($cacheKey));
        $this->assertSame($expectedAdminCount, $this->cache->get($cacheKey));
    }
}
