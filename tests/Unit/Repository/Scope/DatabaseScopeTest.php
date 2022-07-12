<?php

declare(strict_types=1);

namespace Dbus\Tests\Unit\Repository\Scope;

use Dbus\Tests\Support\Context;
use Dbus\Tests\TestCase;

final class DatabaseScopeTest extends TestCase
{
    use Context\Unit\BuilderContext;
    use Context\Unit\ScopeContext;

    public function testToClosureUsesApplyMethodToEvaluate(): void
    {
        $scope = $this->mockDatabaseScope(
            true
        ); // true as arg set expectation of apply method call
        $closure = $scope->toClosure();

        $closure($this->mockQueryBuilder());
    }
}
