<?php

declare(strict_types=1);

namespace Dbus\Repository\Exception;

use Dbus\Repository\Contract\DatabaseRepository;
use RuntimeException;

final class DatabaseRepositoryException extends RuntimeException
{
    public static function connectionIsNotSet(DatabaseRepository $repository): self
    {
        return new self(
            sprintf('Database connection on [%s] repository is not set.', get_class($repository))
        );
    }

    public static function cacheIsNotSet(DatabaseRepository $repository): self
    {
        return new self(sprintf('Cache on [%s] repository is not set.', get_class($repository)));
    }
}
