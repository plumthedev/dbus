<?php

declare(strict_types=1);

namespace Dbus\Tests\Unit\Repository\Scope;

use Dbus\Tests\Support\Context;
use Dbus\Tests\TestCase;

final class ScopeTest extends TestCase
{
    use Context\Unit\ScopeContext;
    use Context\ReflectionContext;

    public function testScopeIdentifierIsClassNameByDefault(): void
    {
        $scope = $this->mockScope();

        $this->assertEquals(get_class($scope), $scope->getIdentifier());
    }

    public function testScopeIdentifierCanBeSetByIdProperty(): void
    {
        $scopeId = 'super_extra_scope_id';
        $scope = $this->mockScope();
        $idProperty = $this->reflectProperty($scope, 'id');
        $idProperty->setValue($scope, $scopeId);

        $this->assertEquals($scopeId, $scope->getIdentifier());
    }
}
