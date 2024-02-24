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

Note that in box.json, the `"check-requirements"` option is set to `false`. Somehow, keeping it as `true` results in a
.phar file
that breaks HTTP requests in Playground. @TODO: Investigate why this is the case.

To try the built .phar file, run:

```shell
rm -rf new-wp/* && USE_PHAR=1 php blueprint_compiling.php
```

