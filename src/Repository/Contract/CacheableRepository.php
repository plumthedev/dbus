<?php

declare(strict_types=1);

namespace Dbus\Repository\Contract;

use DateTimeInterface;
use Illuminate\Contracts\Cache\Repository as Cache;

interface CacheableRepository
{
    public function setCache(Cache $cache): self;

    public function getCache(): Cache;

    /** @return mixed */
    public function cache(string $key, DateTimeInterface $ttl, callable $callback, bool $withScopes = true);
}
