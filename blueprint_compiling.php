<?php

use Swaggest\JsonDiff\JsonPointer;
use Swaggest\JsonSchema\InvalidValue;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructureContract;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\Builder\InstallPluginOptionsBuilder;
use WordPress\Blueprints\ResourceManager;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintPreferredVersionsBuilder;
use WordPress\Blueprints\Model\Builder\LiteralReferenceBuilder;
use WordPress\Blueprints\Model\Builder\UnzipStepBuilder;
use WordPress\Blueprints\Model\Builder\UrlReferenceBuilder;
use WordPress\Blueprints\Model\Builder\VFSReferenceBuilder;
use WordPress\Blueprints\Model\Builder\WordPressInstallationOptionsBuilder;
use WordPress\Blueprints\Model\Builder\WriteFileStepBuilder;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use WordPress\Blueprints\Model\DataClass\LiteralReference;
use WordPress\Blueprints\Model\DataClass\UrlReference;
use WordPress\Blueprints\Model\DataClass\VFSReference;
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

registerBlueprintStepHandler( \WordPress\Blueprints\StepHandler\Implementation\UnzipStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\WriteFileStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\RunPHPStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\DefineWpConfigConstsStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\EnableMultisiteStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\DefineSiteUrlStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\RmDirStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\RmStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\MvStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\CpStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\WPCLIStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\SetSiteOptionsStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\ActivatePluginStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\ActivateThemeStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\InstallPluginStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\InstallThemeStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\ImportFileStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\RunWordPressInstallerStepHandler::class );
registerBlueprintStepHandler( WordPress\Blueprints\StepHandler\Implementation\RunSQLStepHandler::class );

$builder = new BlueprintBuilder();
$builder
	->setPreferredVersions(
		( new BlueprintPreferredVersionsBuilder() )
			->setPhp( '7.4' )
			->setWp( '5.3' )
	)
	->setPlugins( [
		'akismet',
		'hello-dolly',
	] )
	->setPhpExtensionBundles( [
		'kitchen-sink',
	] )
	->setLandingPage( "/wp-admin" )
	->setSteps( [
//		( new \WordPress\Blueprints\Model\Builder\RunWordPressInstallerStepBuilder() )->setOptions(
//			( new WordPressInstallationOptionsBuilder() )
//				->setAdminUsername( 'admin' )
//				->setAdminPassword( 'password' )
//		),
		( new \WordPress\Blueprints\Model\Builder\RunPHPStepBuilder() )
			->setCode( '<?php echo "A"; ' ),
		( new \WordPress\Blueprints\Model\Builder\SetSiteOptionsStepBuilder() )
			->setOptions( (object) [
				'blogname' => 'My Playground Blog',
			] ),
		( new \WordPress\Blueprints\Model\Builder\DefineWpConfigConstsStepBuilder() )
			->setConsts( (object) [
				'WP_DEBUG'         => true,
				'WP_DEBUG_LOG'     => true,
				'WP_DEBUG_DISPLAY' => true,
				'WP_CACHE'         => true,
			] ),
		( new \WordPress\Blueprints\Model\Builder\ActivatePluginStepBuilder() )
			->setSlug( 'hello-dolly' ),
//		( new \WordPress\Blueprints\Model\Builder\InstallPluginStepBuilder() )
//			->setPluginZipFile( 'https://downloads.wordpress.org/plugin/hello-dolly.zip' )
//			->setOptions( ( new InstallPluginOptionsBuilder() )->setActivate( true ) ),
//		( new \WordPress\Blueprints\Model\Builder\InstallThemeStepBuilder() )
//			->setThemeZipFile( 'https://downloads.wordpress.org/theme/pendant.zip' )
//			->setOptions( ( new \WordPress\Blueprints\Model\Builder\InstallThemeStepOptionsBuilder() )->setActivate( true ) ),
//		( new \WordPress\Blueprints\Model\Builder\ImportFileStepBuilder() )
//			->setFile( 'https://raw.githubusercontent.com/WordPress/theme-test-data/master/themeunittestdata.wordpress.xml' ),
//		( new \WordPress\Blueprints\Model\Builder\InstallPluginStepBuilder() )
//			->setPluginZipFile( 'https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip' )
//			->setOptions( ( new InstallPluginOptionsBuilder() )->setActivate( true ) ),
		( new \WordPress\Blueprints\Model\Builder\DefineSiteUrlStepBuilder() )
			->setSiteUrl( 'http://localhost:8080' ),
//		( new \WordPress\Blueprints\Model\Builder\RunSQLStepBuilder() )
//			->setSql( ( new LiteralReferenceBuilder() )->setName( 'file.sql' )->setContents(
//				<<<'SQL'
//CREATE TABLE `tmp_table` ( id INT );
//INSERT INTO `tmp_table` VALUES (1);
//INSERT INTO `tmp_table` VALUES (2);
//SQL
//
//			) ),
		( new WriteFileStepBuilder() )
			->setContinueOnError( true )
			->setPath( 'wordpress.txt' )
			->setData( ( new LiteralReferenceBuilder() )->setContents( "Data" )->setName( "A" ) ),
//		( ( new UnzipStepBuilder() )
//			->setZipFile(
//				'https://wordpress.org/latest.zip'
////				( new UrlReferenceBuilder() )->setUrl( 'https://wordpress.org/latest.zip' )
//			) )
//			->setExtractToPath( __DIR__ . '/outdir2' ),
//		( new WriteFileStepBuilder() )
//			->setPath( __DIR__ . '/outdir2/test.zip' )
//			->setData( 'https://wordpress.org/latest.zip' ),
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

function get_subschema( $schema, $pointer ) {
	if ( $pointer[0] === '#' ) {
		$pointer = substr( $pointer, 1 );
	}
	if ( $pointer[0] !== '/' ) {
		$pointer = substr( $pointer, 1 );
	}
	$path      = explode( '/', substr( $pointer, 1 ) );
	$subSchema = json_decode( file_get_contents( __DIR__ . '/src/WordPress/Blueprints/schema.json' ) );
	foreach ( $path as $key ) {
		if ( is_numeric( $key ) && ! property_exists( $subSchema, $key ) ) {
			foreach ( $subSchema->anyOf as $v ) {
				if ( is_object( $v ) && property_exists( $v, '$ref' ) ) {
					$subSchema = $v;
					break;
				}
			}
		} else {
			$subSchema = $subSchema->$key;
		}
	}

	return $subSchema;
}

/**
 * Narrows down ambiguous anyOf errors using the discriminator property.
 *
 * When one of the `anyOf` inputs doesn't match the schema, Swaggest\JsonSchema will return as many errors,
 * as there are `anyOf` options. Sometimes that means 26 errors to sieve through. For example:
 *
 * ```
 *  No valid results for oneOf {
 *  0: Enum failed, enum: ["a"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[0]
 *  1: Enum failed, enum: ["b"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[1]
 *  2: No valid results for anyOf {
 *  0: Enum failed, enum: ["c"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]->anyOf[0]
 *  1: Enum failed, enum: ["d"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]->anyOf[1]
 *  2: Enum failed, enum: ["e"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]->anyOf[2]
 *  } at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]
 *  } at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo
 * ```
 *
 * It's highly impractical to reason about that much output, so we narrow down the error to the specific `anyOf` option that failed.
 *
 * This function uses the discriminator property to find the specific `anyOf` option that failed.
 * For example, if the data looks like this:
 *
 * ```
 * {
 *     "steps": [
 *          { "step": "activatePlugin", "plugin": null },
 *       ]
 * }
 * ```
 *
 * And the schema looks like this:
 *
 * ```
 * "StepDefinition": {
 *     "type": "object",
 *     "discriminator": {
 *         "propertyName": "step"
 *     },
 *     "oneOf": [
 *         { "$ref": "#/definitions/ActivatePluginStep" },
 *         { "$ref": "#/definitions/ActivateThemeStep" },
 * ```
 *
 * This function will go through all the errors reported by Swaggest\JsonSchema and find the one associated
 * with the `ActivatePluginStep` definition, since its `step` property is set to `activatePlugin`.
 *
 * @param InvalidValue $e
 *
 * @return \Swaggest\JsonSchema\Exception\Error|void|null
 */
function getSpecificAnyOfError( InvalidValue $e ) {
	// Narrows down ambiguous anyOf errors.
	// from – find the actual definition that failed
	// using the discriminator property.
	//// Swaggest\JsonSchema unfortunately
	// won't attempt to narrow down the error to the specific anyOf option
	// that failed.
	$schema    = json_decode( file_get_contents( __DIR__ . '/src/WordPress/Blueprints/schema.json' ) );
	$subSchema = get_subschema( $schema, $e->getSchemaPointer() );

	if ( property_exists( $subSchema, '$ref' ) ) {
		$discriminatedDefinition = get_subschema( $schema, $subSchema->{'$ref'} );
		if ( property_exists( $discriminatedDefinition, 'discriminator' ) ) {
			$discriminatorField = $discriminatedDefinition->discriminator->propertyName;
			$discriminatorValue = $e->data->$discriminatorField;

			foreach ( $discriminatedDefinition->oneOf as $discriminatorOption ) {
				if ( property_exists( $discriminatorOption, '$ref' ) ) {
					$optionDefinition         = get_subschema( $schema, $discriminatorOption->{'$ref'} );
					$optionDiscriminatorValue = $optionDefinition->properties->{$discriminatorField}->const;
					if ( $optionDiscriminatorValue === $discriminatorValue ) {
						return findSubErrorForSpecificAnyOfOption( $e->inspect(), $discriminatorOption->{'$ref'} );
					}
				}
			}
		}
	}
}

function findSubErrorForSpecificAnyOfOption( \Swaggest\JsonSchema\Exception\Error $e, string $anyOfRef ) {
	if ( $anyOfRef[0] === '#' ) {
		$anyOfRef = substr( $anyOfRef, 1 );
	}
	if ( $e->schemaPointers ) {
		foreach ( $e->schemaPointers as $pointer ) {
			if ( str_starts_with( $pointer, $anyOfRef ) ) {
				return $e;
			}
		}
	}
	if ( ! $e->subErrors ) {
		return;
	}
	foreach ( $e->subErrors as $subError ) {
		$subError = findSubErrorForSpecificAnyOfOption( $subError, $anyOfRef );
		if ( $subError !== null ) {
			return $subError;
		}
	}
}

replaceUrlsWithResourceObjects( $builder );

try {
	$builder->validate();
} catch ( InvalidValue $rootError ) {
	$errorReport = [
		"dataPointer"   => $rootError->getDataPointer(),
		"schemaPointer" => $rootError->getSchemaPointer(),
		"Message"       => $rootError->getMessage(),
		"Data"          => $rootError->data,
		"Constraint"    => $rootError->constraint,
	];

	$specificError = getSpecificAnyOfError( $rootError );
	if ( $specificError ) {
		$errorReport['Message'] = $specificError->error;
	}

	print_r( $errorReport );
	die();
}


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

$resourceMap = new ResourceManager();
foreach ( $resources as $path => $resourceDeclaration ) {
	if ( $resourceDeclaration instanceof LiteralReference ) {
		$fp = fopen( "php://temp", 'r+' );
		fwrite( $fp, $resourceDeclaration->contents );
		rewind( $fp );
	} elseif ( $resourceDeclaration instanceof UrlReference ) {
		$fp = $container['data_source.url']->stream( $resourceDeclaration->url );
	} elseif ( $resourceDeclaration instanceof VFSReference ) {
		$fp = fopen( $resourceDeclaration->path, 'r' );
	} else {
		throw new \InvalidArgumentException( "Unknown resource type " . $resourceDeclaration->resource );
	}
	$resourceMap[ $resourceDeclaration ] = $fp;
}

print_r( $resources );
$runtime = new \WordPress\Blueprints\Runtime\NativePHPRuntime(
	__DIR__ . '/outdir/wordpress'
);

$compiledSteps = [];
foreach ( $blueprint->steps as $step ) {
	if ( empty( $stepMeta[ $step->step ] ) ) {
		throw new \InvalidArgumentException( "No handler for step {$step->step}" );
	}
	$handler = $stepMeta[ $step->step ]['factory']();
	/** @var $handler \WordPress\Blueprints\StepHandler\BaseStepHandler */
	$handler->setResourceMap( $resourceMap );
	$handler->setRuntime( $runtime );
	$compiledSteps[] = function ( $progressTracker = null ) use ( $step, $stepMeta, $handler, $resourceMap ) {
		return $handler->execute( $step, $progressTracker );
	};
}

class StepResult {

}

class StepSuccess extends StepResult {

	public function __construct( public $step, public $result ) {
	}
}

class StepFailure extends StepResult {

	public function __construct( public $step, public \Exception $exception ) {
	}
}

$results = [];
foreach ( $compiledSteps as $k => $runStep ) {
	$step = $blueprint->steps[ $k ];
	try {
		$results[ $k ] = new StepSuccess( $step, $runStep() );
	} catch ( \Exception $e ) {
		if ( $step->continueOnError === true ) {
			$results[ $k ] = new StepFailure( $step, $e );
		} else {
			throw $e;
		}
	}
}

var_dump( $results );
