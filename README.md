## Setup

Install composer dependencies:

```shell
composer install
```

Run tests with

```shell
vendor/bin/pest
```

Setup WordPress with SQLite using asynchronous resource fetching as follows:

```shell
php setup_wordpress.php
cd outdir/wordpress  
php wp-cli.phar core install --url=localhost:8550 --title="My test site" --admin_user=admin --admin_email="admin@localhost.com" --admin_password="password"
php wp-cli.phar server --port=8550 
```

Blueprint parsing demo:

```shell
php parse_blueprint.php
```
