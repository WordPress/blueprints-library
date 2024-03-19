<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\ValueObject\PhpVersion;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPhpVersion(PhpVersion::PHP_70)
    ->withPaths([
        __DIR__ . '/src',
    ])
	->withSets([
		DowngradeLevelSetList::DOWN_TO_PHP_71
	])
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
    ]);
