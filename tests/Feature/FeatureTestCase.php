<?php

declare(strict_types=1);

namespace Dbus\Tests\Feature;

use Dbus\Tests\Support\Database\SetupDatabase;
use Dbus\Tests\TestCase;

abstract class FeatureTestCase extends TestCase
{
    use SetupDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase($this->app);
    }
}
