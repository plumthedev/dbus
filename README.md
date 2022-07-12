# DBUS

DBUS is an implementation of 
[repository pattern](https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html) for 
[Laravel framework](https://laravel.com/) done in good way.  
Supports 
[raw database queries](https://laravel.com/docs/8.x/queries), 
[Eloquent models](https://laravel.com/docs/8.x/eloquent) and 
[cache](https://laravel.com/docs/8.x/cache) out-of-the-box.

## Installation
Package is available via Composer packagist.org. 
You can install it from CLI.

```bash
$ composer require plumthedev/dbus
```

## Documentation
DBUS provides public interfaces which can be used by you.
If you would like to use the full potential of the package, please read the [documentation](./DOCS.md).

## Usage
For usage examples please see [examples](./examples) directory on this repository.

## Running tests
DBUS have [unit](./tests/Unit) and [feature](./tests/Feature) tests. 
If you are contributing to this project, please make sure about if tests are passing.
If you make new feature, please be sure that you wrote tests for it.

## Contributing
Contributions are always welcome!

## License
Open Source based on MIT license.