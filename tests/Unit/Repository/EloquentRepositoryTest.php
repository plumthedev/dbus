<?php

declare(strict_types=1);

namespace Dbus\Tests\Unit\Repository;

use Dbus\Repository\Contract\Scope\EloquentScope;
use Dbus\Tests\Support\Context;
use Dbus\Tests\TestCase;

final class EloquentRepositoryTest extends TestCase
{
    use Context\Unit\EloquentRepositoryContext;
    use Context\Unit\ScopeContext;
    use Context\ReflectionContext;

    public function testQueryIsBuiltBasedOnModel(): void
    {
        $table = 'some_table';
        $model = $this->mockEloquentModel($table);
        $repository = $this->mockEloquentRepository($model);
        $query = $repository->getQuery();
        $queryBuilder = $query->getQuery(); // base builder instance

        $this->assertSame($model, $query->getModel());

        $this->assertSame($table, $query->from);
        $this->assertSame($table, $queryBuilder->from);

        $this->assertSame($model->getConnection(), $query->connection);
        $this->assertSame($model->getConnection(), $queryBuilder->connection);

        $this->assertSame($model->getConnection(), $repository->getConnection());
    }

    public function testQueryAppliesScopesByDefault(): void
    {
        $scope = $this->mockQueryScope(true);
        $repository = $this->mockEloquentRepository();
        $repository->withQueryScope($scope);

        $query = $repository->getQuery();

        $queryScopesProperty = $this->reflectProperty($query, 'scopes');
        $queryScopes = $queryScopesProperty->getValue($query);

        $this->assertArrayHasKey($scope->getIdentifier(), $queryScopes);
    }

    public function testQueryScopesCanBeRemovedEasily(): void
    {
        $scope = $this->mockQueryScope();
        $repository = $this->mockEloquentRepository();
        $repository->withQueryScope($scope);

        $query = $repository->getQuery(false);

        $queryScopesProperty = $this->reflectProperty($query, 'scopes');
        $queryScopes = $queryScopesProperty->getValue($query);

        $this->assertEmpty($queryScopes);
    }

    public function testQueryScopesAreEmptyByDefault(): void
    {
        $this->assertEmpty($this->mockEloquentRepository()->getQueryScopes());
    }

    public function testSetQueryScopesPassOnlyEloquentScopeInstances(): void
    {
        $scopes = ['invalid scope', $this->mockQueryScope()];

        $repository = $this->mockEloquentRepository();
        $repositoryScopes = $repository->setQueryScopes($scopes)->getQueryScopes();

        $this->assertNotSameSize($scopes, $repositoryScopes);
        $this->assertCount(1, $repositoryScopes);
        $this->assertContainsOnlyInstancesOf(EloquentScope::class, $repositoryScopes);
    }

    public function testWithQueryScopeAddScopeWithIdentifierAsKey(): void
    {
        $scope = $this->mockQueryScope();
        $repository = $this->mockEloquentRepository();

        $repositoryScopes = $repository->withQueryScope($scope)->getQueryScopes();

        $this->assertArrayHasKey($scope->getIdentifier(), $repositoryScopes);
    }
}
