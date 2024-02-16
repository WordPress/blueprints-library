<?php

require 'vendor/autoload.php';

use WordPress\Blueprints\Dependency\ContainerBuilder;
use WordPress\Blueprints\Steps\Mkdir\MkdirStep;
use WordPress\Blueprints\Steps\Mkdir\MkdirStepDefinition;
use WordPress\Blueprints\Steps\Unzip\UnzipStep;
use WordPress\Blueprints\Steps\Unzip\UnzipStepDefinition;
use WordPress\Blueprints\Steps\WriteFile\WriteFileStep;
use WordPress\Blueprints\Steps\WriteFile\WriteFileStepDefinition;

$builder   = new ContainerBuilder();
$container = $builder->build( 'native' );

var_dump(
	$container['json_schema_compiler']->build()
);
