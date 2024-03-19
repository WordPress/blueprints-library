.PHONY: clean_deps test phpcs phpcs-fixer phpcbf

clean_deps:
	rm -rf vendor

install_web_deps:
	make clean_deps
	COMPOSER=composer-web-7-0.json composer install --no-dev

install_dev_deps:
	make clean_deps
	COMPOSER=composer-dev.json composer install --dev

dist/blueprints.phar: install_web_deps
	find src \(\
		-name '*.md' -o \
		-name 'README.*' -o \
		-name 'LICENSE' -o \
		-name 'CHANGELOG' -o \
		-name '.travis.yml' -o \
		-name 'phpunit.xml*' -o \
		-name 'package.json' -o \
		-name 'Tests' -o \
		-name 'composer.json' \) \
		-exec rm -rf {} \+
	box compile
	cd dist && zip blueprints.zip blueprints.phar
	git reset --hard

bundle_web: dist/blueprints.phar

clean_phar:
	rm -rf dist/blueprints.phar
	
clean: clean_phar
	make clean_deps

test:
	phpunit

phpcs:
	phpcs ./src/WordPress -s

phpcs-fixer:
	php-cs-fixer fix ./src/WordPress

phpcbf:
	phpcbf ./src/WordPress

