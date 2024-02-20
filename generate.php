<?php

require 'vendor/autoload.php';

use PHPModelGenerator\Model\GeneratorConfiguration;
use PHPModelGenerator\ModelGenerator;
use PHPModelGenerator\SchemaProvider\RecursiveDirectoryProvider;

$generator = new ModelGenerator(
	( new GeneratorConfiguration() )
		->setNamespacePrefix( 'MyApp\Model' )
		->setImmutable( false )
		->setDenyAdditionalProperties( true )
);

$generator
	->generateModelDirectory( __DIR__ . '/result' )
	->generateModels( new RecursiveDirectoryProvider( __DIR__ . '/src/WordPress/Blueprints' ), __DIR__ . '/result' );


