## Setup

Install [composer](https://getcomposer.org/) and the required composer dependencies via

```shell
composer install
```

## Useful commands

Run tests with

```shell
vendor/bin/pest
```

Regenerate models from JSON schema with

```shell
 php src/WordPress/Blueprints/bin/autogenerate_models.php
```

Run a Blueprint with

```shell
php blueprint_compiling.php
```

## Building to .phar

The Blueprints library is distributed as a .phar library. To build the .phar file, run:

```shell
vendor/bin/box compile
```

To try the built .phar file, run:

```shell
rm -rf new-wp/* && USE_PHAR=1 php blueprint_compiling.php
```

