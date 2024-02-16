<?php

use Swaggest\JsonSchema\Structure\ClassStructureContract;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Map;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintPreferredVersionsBuilder;
use WordPress\Blueprints\Model\Builder\LiteralReferenceBuilder;
use WordPress\Blueprints\Model\Builder\ProgressBuilder;
use WordPress\Blueprints\Model\Builder\UnzipStepBuilder;
use WordPress\Blueprints\Model\Builder\UrlReferenceBuilder;
use WordPress\Blueprints\Model\Builder\VFSReferenceBuilder;
use WordPress\Blueprints\Model\Builder\WriteFileStepBuilder;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use WordPress\Blueprints\Model\DataClass\LiteralReference;
use WordPress\Blueprints\Model\DataClass\UrlReference;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;

require 'vendor/autoload.php';

$stepMeta = [];
function registerBlueprintStepHandler( $stepHandlerClass, array $details = [] ) {
	global $stepMeta;

	if ( empty( $details['model'] ) ) {
		// Get the type of the first parameter of the execute method
		$reflection = new \ReflectionMethod( $stepHandlerClass, 'execute' );
		$parameters = $reflection->getParameters();
		if ( $parameters[0] ) {
			$details['model'] = $parameters[0]->getType()->getName();
		}
	}
	if ( empty( $details['model'] ) || ! class_exists( $details['model'] ) ) {
		throw new \InvalidArgumentException( "Could not determine input class for $stepHandlerClass" );
	}

	$details['slug'] = $details['slug'] ?? ( new \ReflectionClass( $details['model'] ) )->getProperty( 'step' )->getDefaultValue();

	if ( empty( $details['factory'] ) ) {
		$details['factory'] = function () use ( $stepHandlerClass ) {
			return new $stepHandlerClass();
		};
	}

	$stepMeta[ $details['slug'] ] = $details;
}

registerBlueprintStepHandler( WordPress\Blueprints\Steps\UnzipStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\Steps\WriteFileHandler::class );


$builder = new BlueprintBuilder();
$builder
	->setPreferredVersions(
		( new BlueprintPreferredVersionsBuilder() )
			->setPhp( '7.4' )
			->setWp( '5.3' )
	)
	->setLandingPage( "/wp-admin" )
	->setSteps( [
		( new WriteFileStepBuilder() )
			->setProgress( ( new ProgressBuilder() )
				->setCaption( "Logging in" )
				->setWeight( 3 )
			)
			->setPath( __DIR__ . '/test.txt' )
			->setData( ( new LiteralReferenceBuilder() )->setContents( "Data" )->setName( "A" ) ),
		( ( new UnzipStepBuilder() )
			->setZipFile(
				'https://wordpress.org/latest.zip'
//				( new UrlReferenceBuilder() )->setUrl( 'https://wordpress.org/latest.zip' )
			) )
			->setExtractToPath( __DIR__ . '/outdir2' ),
		( new WriteFileStepBuilder() )
			->setPath( __DIR__ . '/outdir2/test.zip' )
			->setData( 'https://wordpress.org/latest.zip' ),
	] );

function replaceUrlsWithResourceObjects( $jsonData ) {
	if ( ! ( $jsonData instanceof ClassStructureContract ) || $jsonData instanceof WriteFileStep ) {
		return;
	}
	foreach ( $jsonData::schema()->getProperties() as $key => $value ) {
		if ( is_string( $jsonData->$key ) ) {
			if ( $jsonData::schema()->getProperty( $key )->getFromRef() == '#/definitions/FileReference' ) {
				if ( str_starts_with( $jsonData->$key, 'https://' ) ) {
					$jsonData->$key = ( new UrlReferenceBuilder() )->setUrl( $jsonData->$key );
				} elseif ( str_starts_with( $jsonData->$key, 'file://' ) || str_starts_with( $jsonData->$key, './' ) ) {
					$jsonData->$key = ( new VFSReferenceBuilder() )->setPath( $jsonData->$key );
				} else {
					$jsonData->$key = ( new LiteralReferenceBuilder() )->setName( "literal" )->setContents( $jsonData->$key );
				}
			}
		} elseif ( is_object( $jsonData->$key ) ) {
			replaceUrlsWithResourceObjects( $jsonData->$key );
		} elseif ( is_array( $jsonData->$key ) ) {
			foreach ( $jsonData->$key as $k => $v ) {
				replaceUrlsWithResourceObjects( $v );
			}
		}
	}
}

replaceUrlsWithResourceObjects( $builder );
$builder->validate();

// The Blueprint is valid!
$blueprint = $builder->toDataObject();

// Find all the resources in the blueprint
function findResources( $jsonData, &$resources, $path = '' ) {
	if ( $jsonData instanceof FileReferenceInterface ) {
		$resources[ $path ] = $jsonData;
	} elseif ( is_object( $jsonData ) ) {
		foreach ( get_object_vars( $jsonData ) as $key => $value ) {
			findResources( $value, $resources, $path . '->' . $key );
		}
	} elseif ( is_array( $jsonData ) ) {
		foreach ( $jsonData as $k => $v ) {
			findResources( $v, $resources, $path . '[' . $k . ']' );
		}
	} else {
		return $jsonData;
	}
}


$resources = [];
findResources( $blueprint, $resources );

$container = ContainerBuilder::build( 'native' );

$resourceMap = new Map();
foreach ( $resources as $path => $resourceDeclaration ) {
	if ( $resourceDeclaration instanceof LiteralReference ) {
		$fp = fopen( "php://temp", 'r+' );
		fwrite( $fp, $resourceDeclaration->contents );
	} elseif ( $resourceDeclaration instanceof UrlReference ) {
		$fp = $container['data_source.url']->stream( $resourceDeclaration->url );
	} else {
		throw new \InvalidArgumentException( "Unknown resource type " . $resourceDeclaration->resource );
	}
	rewind( $fp );
	$resourceMap[ $resourceDeclaration ] = $fp;
}

print_r( $resources );

$compiledSteps = [];
foreach ( $blueprint->steps as $step ) {
	if ( empty( $stepMeta[ $step->step ] ) ) {
		throw new \InvalidArgumentException( "No handler for step {$step->step}" );
	}
	$compiledSteps[] = function () use ( $step, $stepMeta, $resourceMap ) {
		$handler = $stepMeta[ $step->step ]['factory']();
		$handler->setResourceMap( $resourceMap );
		$handler->execute( $step, null );
	};
}

foreach ( $compiledSteps as $step ) {
	$step();
}
