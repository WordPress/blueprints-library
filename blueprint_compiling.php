<?php

use Swaggest\JsonSchema\InvalidValue;
use Swaggest\JsonSchema\Structure\ClassStructureContract;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\ResourceManager;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintPreferredVersionsBuilder;
use WordPress\Blueprints\Model\Builder\InlineResourceBuilder;
use WordPress\Blueprints\Model\Builder\WriteFileStepBuilder;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;

require 'vendor/autoload.php';

$builder   = new ContainerBuilder();
$container = $builder->build( 'native' );

$resourceResolver = $container['resource.resolver'];

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
//			->setSql( ( new LiteralReferenceBuilder() )->setContents(
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
			->setData( ( new InlineResourceBuilder() )->setContents( "Data" ) ),
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
	global $resourceResolver;
	if ( ! ( $jsonData instanceof ClassStructureContract ) || $jsonData instanceof WriteFileStep ) {
		return;
	}
	foreach ( $jsonData::schema()->getProperties() as $key => $value ) {
		if ( is_string( $jsonData->$key ) ) {
			if ( $jsonData::schema()->getProperty( $key )->getFromRef() == '#/definitions/FileReference' ) {
				$resource = $resourceResolver->parseUrl( $jsonData->$key );
				if ( false === $resource ) {
					throw new \InvalidArgumentException( "Could not parse resource {$jsonData->$key}" );
				}
				$jsonData->$key = $resource;
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


// @TODO: $container['resource.manager']->enqueue($resources);
$resourceMap = new ResourceManager();
foreach ( $resources as $resourceDeclaration ) {
	$resourceMap[ $resourceDeclaration ] = $resourceResolver->stream( $resourceDeclaration );
}

print_r( $resources );
$runtime = new \WordPress\Blueprints\Runtime\NativePHPRuntime(
	__DIR__ . '/outdir/wordpress'
);


interface StepResult {

}

class StepSuccess implements StepResult {

	public function __construct( public \WordPress\Blueprints\Model\DataClass\StepInterface $step, public $result ) {
	}
}

class StepFailure extends \WordPress\Blueprints\BlueprintException implements StepResult {

	public function __construct(
		public \WordPress\Blueprints\Model\DataClass\StepInterface $step,
		\Exception $cause
	) {
		parent::__construct( "Error when executing step $step->step", 0, $cause );
	}
}

class BlueprintExecutionException extends \WordPress\Blueprints\BlueprintException {
}

// Compile, ensure all the runners may be created and configured
$compiledSteps = [];
foreach ( $blueprint->steps as $step ) {
	$runner = $container['step.runner_factory']( $step->step );
	/** @var $runner \WordPress\Blueprints\StepRunner\BaseStepRunner */
	$runner->setResourceMap( $resourceMap );
	$runner->setRuntime( $runtime );
	$compiledSteps[] = function ( $progressTracker = null ) use ( $step, $runner, $resourceMap ) {
		return $runner->run( $step, $progressTracker );
	};
}

// Run, store results
$results = [];
foreach ( $compiledSteps as $k => $runStep ) {
	$step = $blueprint->steps[ $k ];
	try {
		$results[ $k ] = new StepSuccess( $step, $runStep() );
	} catch ( \Exception $e ) {
		$results[ $k ] = new StepFailure( $step, $e );
		if ( $step->continueOnError !== true ) {
			throw new BlueprintExecutionException(
				"Error when executing step $step->step (number $k on the list)",
				0,
				$results[ $k ]
			);
		}
	}
}

var_dump( $results );
