# WordPress Blueprints

Blueprints are JSON files used to create WordPress sites with specific settings, themes, plugins, content, and anything
else that can be configured in WordPress. Here's what a Blueprint looks like:

```json
{
  "plugins": [
    "akismet",
    "gutenberg"
  ],
  "themes": [
    "twentynineteen"
  ],
  "settings": {
    "blogname": "My Blog",
    "blogdescription": "Just another WordPress site",
    "permalink_structure": "/%postname%/"
  },
  "constants": {
    "WP_DEBUG": true,
    "WP_DEBUG_LOG": true
  },
  "steps": [
    {
      "step": "runPHP",
      "content": "<?php require 'wp-load.php'; update_user_meta(1, 'test', 'value');"
    }
  ]
}
```

See the
original [Getting started with Blueprints v1](https://wordpress.github.io/wordpress-playground/blueprints-api/index)
page for more information.

## Let's build Version 2 of Blueprints together!

Blueprints were initially built in TypeScript
for [WordPress Playground](https://github.com/WordPress/wordpress-playground/),
However, they [quickly proved useful for
WordPress in general](https://github.com/WordPress/wordpress-playground/issues/1025).

This repository explores
a [PHP-based version 2 of Blueprints](https://github.com/WordPress/wordpress-playground/issues/1025) that can be
used in any environment, be it a browser, Node.js, wp-cli, or a native PHP application. The plan is to create a robust,
useful tool that will eventually be merged into WordPress core.

Your feedback is not just welcome, but essential to the success of this project. Please:

* Share your thoughts and ideas in the [Blueprints v2 Specification](https://github.com/WordPress/blueprints/issues/6)
  issue – or any other issue that interests you
* Start new discussions
* Propose changes through comments and pull requests

Your input and code contributions will help shape the future of Blueprints in WordPress.
in discussions.

# Technical bits

## Setting Up the Project Locally

To set up the WordPress/blueprints project locally, here's a few useful commands:

Install [composer](https://getcomposer.org/). Once you have it, install the required dependencies via

```shell
composer install
```

### Run tests with

```shell
vendor/bin/phpunit --testdox
```

### Run Blueprint in a variety forms

#### from PHP:

```shell
 php examples/blueprint_compiling.php
```

#### from JSON as string:

```shell
 php examples/json_string_compiling.php
```

### Regenerate models files from JSON schema with

```shell
 php src/WordPress/Blueprints/bin/autogenerate_models.php
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

## License

WordPress Blueprints are open-source software licensed under the GPL.
