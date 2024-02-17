## Setup

Install composer dependencies:

```shell
composer install
```

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

Setup WordPress with SQLite using asynchronous resource fetching as follows:

```shell
php setup_wordpress.php
cd outdir/wordpress  
php wp-cli.phar core install --url=localhost:8550 --title="My test site" --admin_user=admin --admin_email="admin@localhost.com" --admin_password="password"
php wp-cli.phar server --port=8550 
```
