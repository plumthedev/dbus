# DBUS Documentation

## Repositories

### How to use database repository?

Firstly you need to implement `getTable` method in your repository class.

```php
<?php

use Dbus\Repository\DatabaseRepository;

class MyRepository extends DatabaseRepository 
{
    public function getTable(): string
    {
        return 'database_table_name';     
    }
}
```

Then it is depends on your way of creating repository instance. If you are using DI, you don't have to do anything else.

If you are creating repository instance in traditional way, you have to set `Illuminate\Database\ConnectionInterface`
and `Illuminate\Contracts\Cache\Repository` via repository setters.

```php
<?php 
$repository = new MyRepository();

$repository->setConnection($connection); 
$repository->setCache($cache); 
```

Nevertheless, I suggest using DI, it is easier to get job done.

### How to use Eloquent repository?

Eloquent repository instance inheritance all features from `Dbus\Repository\DatabaseRepository`
so all above tips should be applied to it too. In Eloquent repository instance you don't have to return table name. You
have to return model which is related to this repository:

```php
<?php

use App\Model\User;
use Dbus\Repository\EloquentRepository;

class MyRepository extends EloquentRepository 
{
    public function getModel(): string
    {
        return new User();     
    }
}
```

Of course, you can decide how your model will be returned. You can insert your model via DI in `__construct` and return
it in getter.

```php
<?php

use App\Model\User;
use Dbus\Repository\EloquentRepository;

class MyRepository extends EloquentRepository 
{
    private User $model;
    
    public function __construct(User $model) {
        $this->model = $model;
    }
    
    public function getModel(): string
    {
        return $this->model;     
    }
}
```

### How to cache repository results?

For better user performance you can use included to your repository cache instance. That allows you to cache results
from database and make your application faster. Both base repositories includes cache, so it doesn't matter which one
you are currently using.

Here is an example:

```php
<?php

use App\Model\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Dbus\Repository\EloquentRepository;

class MyRepository extends EloquentRepository 
{
    private User $model;
    
    public function __construct(User $model) {
        $this->model = $model;
    }
    
    public function getModel(): string
    {
        return $this->model;     
    }
    
    public function getActiveUsersCount(): int
    {
        $this->cache('active_users_count', Carbon::now()->addDay(), function (Builder $query) {
            return $query->where('is_active', '=', true)->count('uuid');
        });    
    }
}
```

Cache signature for both repository types is same.

```php
CacheableRepository::cache(string $key, DateTimeInterface $ttl, callable $callback, bool $withScopes = true);
```

Difference between them, is argument passed to `$callback`  
`Dbus\Repository\DatabaseRepository` will get `Illuminate\Database\Query\Builder` instance when\
`Dbus\Repository\EloquentRepository` will get `Illuminate\Database\Eloquent\Builder` instance.

## Scopes

Scope is mechanism to make your code reusable, easier to implement and easier to understand.\
Scopes are great when you want to limit your results multiple times in same way but different places.

For example, let's suppose that in your application you have users with role, 
and you want to count all active users with roles in different methods. 

Firstly let's declare scope.

```php
<?php

use Dbus\Repository\Scope\EloquentScope;
use Illuminate\Database\Eloquent\Builder;

class UserRoleScope extends EloquentScope  
{
   private string $role;
   
   public function __construct(string $role) 
   {
        $this->role = $role;
   }
   
   public static function onlyAdmins(): self
   {
        return new self('admin');    
   }
   
      public static function onlyManagers(): self
   {
        return new self('manager');
   }
   
   public function apply(Builder $query): void
   {
        $query->where('role', '=', $this->role);     
   }
}
```

Then use it in your repository:

```php
<?php
// repository implementation    
public function getAdminsCount(): int
{
    return $this
        ->withQueryScope(UserRoleScope::onlyAdmins())
        ->getQuery()
        ->count('uuid');
}

public function getManagersCount(): int
{
    return $this
        ->withQueryScope(UserRoleScope::onlyManagers())
        ->getQuery()
        ->count('uuid');
}
```

That is how scopes are working.

### Is signature of scope for DatabaseRepository and EloquentRepository the same?

No, they are different.

For `Dbus\Repository\DatabaseRepository` you will use `Dbus\Repository\Scope\DatabaseScope`.\
For `Dbus\Repository\EloquentRepository` you will use `Dbus\Repository\Scope\EloquentScope`.

But `EloquentRepository` can use `DatabaseScope` as well.

```php
<?php
// eloquent repository implementation    
public function getManagersCount(): int
{
    return $this
        ->withBuilderScope(UserRoleScope::onlyManagers())
        ->withQueryScope(UserRoleScope::onlyManagers())
        ->getQuery()
        ->count('uuid');
}
```

It can be used together when you will use `getQuery()` method, 
when you will use `getBuilder()` method on `EloquentRepository` then only `DatabaseScopes` will be applied.

```php
<?php
// eloquent repository implementation    
public function getManagersCount(): int
{
    return $this
        ->withBuilderScope(UserRoleScope::onlyManagers())
        ->withQueryScope(UserRoleScope::onlyManagers()) // will be skipped, because $this->getBuilder() call
        ->getBuilder()
        ->count('uuid');
}
```

### How to skip scopes in queries, even they are declared?
When you want to don't apply scopes to your query, 
just pass `false` as argument to `Dbus\Repository\EloquentRepository::getQuery()` 
or `Dbus\Repository\Database::getBuilder()` methods.

### Can I use scopes with cache?

Of course, you can use it as well together.

```php
<?php
// repository implementation    
public function getManagersCount(): int
{
    return $this
        ->withQueryScope(UserRoleScope::onlyManagers())
        ->cache('managers_count', Carbon::now()->addWeek(), function (Builder $query) {
            return $query->count('uuid');
        });
}
```

Even, if you want to don't apply scopes in cache method callback, pass `false` as fourth argument to `cache()` method.

```php
<?php
// repository implementation    
public function getManagersCount(): int
{
    return $this
        ->withQueryScope(UserRoleScope::onlyManagers())
        ->cache('users_count', Carbon::now()->addWeek(), function (Builder $query) {
            return $query->count('uuid');
        }, false); // scopes will be skipped 
}
```