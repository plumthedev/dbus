<?php

declare(strict_types=1);

namespace Dbus\Provider;

use Dbus\Repository\Contract\CacheableRepository;
use Dbus\Repository\DatabaseRepository;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\ServiceProvider;

final class DbusServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // php74 ./vendor/bin/phpcs -spn --standard=phpcs.xml
        // php74 ./vendor/bin/phpcbf --standard=phpcs.xml
        $this->setupConnectionOnResolvedRepository();
        $this->setupCacheOnResolvedRepository();
    }

    private function setupConnectionOnResolvedRepository(): void
    {
        $this->app->afterResolving(
            DatabaseRepository::class,
            static function (DatabaseRepository $repository, Application $app): void {
                $connection = $app->make(ConnectionInterface::class);

                if (!($connection instanceof ConnectionInterface)) {
                    return;
                }

                $repository->setConnection($connection);
            }
        );
    }

    private function setupCacheOnResolvedRepository(): void
    {
        $this->app->afterResolving(
            CacheableRepository::class,
            static function (CacheableRepository $repository, Application $app): void {
                $cache = $app->make(Cache::class);

                if (!($cache instanceof Cache)) {
                    return;
                }

                $repository->setCache($cache);
            }
        );
    }
}
