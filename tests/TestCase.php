<?php

declare(strict_types=1);

namespace Dbus\Tests;

use Dbus\Provider\DbusServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param \Illuminate\Contracts\Console\Application|mixed $app
     * @return array<int, string>
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    protected function getPackageProviders($app): array
    {
        return [
            DbusServiceProvider::class,
        ];
    }
}
