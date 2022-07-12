<?php

declare(strict_types=1);

namespace Dbus\Tests\Support\Context\Unit;

use Illuminate\Cache\Repository as Cache;
use Mockery;

trait CacheContext
{
    public function mockCache(): Cache
    {
        return Mockery::mock(Cache::class)->makePartial();
    }
}
