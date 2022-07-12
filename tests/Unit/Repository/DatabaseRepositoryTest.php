<?php

declare(strict_types=1);

namespace Dbus\Tests\Unit\Repository;

use Dbus\Repository\Exception\DatabaseRepositoryException;
use Dbus\Repository\Scope\DatabaseScope;
use Dbus\Tests\Support\Context;
use Dbus\Tests\TestCase;
use Illuminate\Database\ConnectionInterface;

final class DatabaseRepositoryTest extends TestCase
{
    use Context\Unit\DatabaseRepositoryContext;
    use Context\Unit\ScopeContext;
    use Context\Unit\CacheContext;

    protected string $tableName = '';

    protected ?ConnectionInterface $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tableName = 'some_table';
        $this->connection = $this->mockConnection();
    }

    public function testBuilderIsBasedOnRepositoryTable(): void
    {
        $table = 'some_table';
        $repository = $this->mockDatabaseRepository($table);
        $repository->setConnection($this->connection);

        $this->assertSame($table, $repository->getBuilder()->from);
    }

    public function testConnectionIsRequiredWhenBuilderIsBuild(): void
    {
        $repository = $this->mockDatabaseRepository($this->tableName);

        $this->expectException(DatabaseRepositoryException::class);
        $repository->getBuilder();
    }

    public function testConnectionCanBeSetBySetterToUseWithBuilder(): void
    {
        $repository = $this->mockDatabaseRepository($this->tableName);
        $repository->setConnection($this->connection);

        $this->assertSame($this->connection, $repository->getBuilder()->connection);
    }

    public function testGetBuilderScopesIsEmptyByDefault(): void
    {
        $this->assertEmpty(
            $this->mockDatabaseRepository($this->tableName)->getBuilderScopes()
        );
    }

    public function testSetBuilderScopesPassOnlyDatabaseScopes(): void
    {
        $repository = $this->mockDatabaseRepository($this->tableName);
        $scopes = ['whatever', $this->mockDatabaseScope()];
        $repositoryScopes = $repository->setBuilderScopes($scopes)->getBuilderScopes();

        $this->assertCount(1, $repositoryScopes);
        $this->assertContainsOnlyInstancesOf(DatabaseScope::class, $repositoryScopes);
    }

    public function testWithBuilderScopeAddScopeWithIdentifierAsKey(): void
    {
        $scope = $this->mockDatabaseScope();
        $repository = $this->mockDatabaseRepository($this->tableName);
        $repositoryScopes = $repository->withBuilderScope($scope)->getBuilderScopes();

        $this->assertArrayHasKey($scope->getIdentifier(), $repositoryScopes);
    }

    public function testBuilderAppliesScopesByDefault(): void
    {
        $scope = $this->mockDatabaseScope();
        $repository = $this->mockDatabaseRepository($this->tableName);
        $repository->setConnection($this->connection);
        $repository->withBuilderScope($scope);

        $beforeQueryCallbacks = $repository->getBuilder()->beforeQueryCallbacks;
        $this->assertNotEmpty($beforeQueryCallbacks);
    }

    public function testBuilderScopesCanBeRemovedEasily(): void
    {
        $scope = $this->mockDatabaseScope();
        $repository = $this->mockDatabaseRepository($this->tableName);
        $repository->setConnection($this->connection);
        $repository->withBuilderScope($scope);

        $beforeQueryCallbacks = $repository->getBuilder(false)->beforeQueryCallbacks;
        $this->assertEmpty($beforeQueryCallbacks);
    }

    public function testCacheIsRequiredWhenYouWantToUseIt(): void
    {
        $repository = $this->mockDatabaseRepository($this->tableName);

        $this->expectException(DatabaseRepositoryException::class);
        $repository->getCache();
    }

    public function testCacheCanBeSetToUseIt(): void
    {
        $repository = $this->mockDatabaseRepository($this->tableName);
        $repository->setCache($cache = $this->mockCache());

        $this->assertSame($cache, $repository->getCache());
    }
}
