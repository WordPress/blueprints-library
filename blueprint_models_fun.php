<?php

use Swaggest\JsonSchema\InvalidValue;
use WordPress\Blueprints\Model\BlueprintDeserializer;
use WordPress\Blueprints\Model\BlueprintSerializer;
use WordPress\Blueprints\Model\Builder\ActivateThemeStepBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintPreferredVersionsBuilder;
use WordPress\Blueprints\Model\Builder\LoginStepBuilder;
use WordPress\Blueprints\Model\Builder\ProgressBuilder;
use WordPress\Blueprints\Model\Builder\UnzipStepBuilder;
use WordPress\Blueprints\Model\Builder\UrlReferenceBuilder;
use WordPress\Blueprints\Model\Builder\WPCLIStepBuilder;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;

require 'vendor/autoload.php';

//print_r( BlueprintBuilder::schema()->getProperties()->steps->items->anyOf[0]->oneOf );
//die();
//BlueprintBuilder::schema()->getProperties()->steps->items->anyOf[0]->oneOf[] = WPCLIStepBuilder::schema();

$builder = new BlueprintBuilder();
$builder
	->setPreferredVersions(
		( new BlueprintPreferredVersionsBuilder() )
			->setPhp( '7.4' )
			->setWp( '5.3' )
	)
	->setLandingPage( "/wp-admin" )
	->setSteps( [
		( new LoginStepBuilder() )
			->setProgress( ( new ProgressBuilder() )
				->setCaption( "Logging in" )
				->setWeight( 3 )
			)
			->setStep( "login" )
			->setUsername( "admin" )
			->setPassword( "password" ),
		( ( new UnzipStepBuilder() )
			->setZipFile(
				( new UrlReferenceBuilder() )->setUrl( 'https://example.com/wordpress.zip' )
			) )
			->setExtractToPath( "/wordpress" ),
	] );
$blueprint = $builder->toDataObject();

// No exception on exporting valid data
// {"landingPage":"\/wp-admin","preferredVersions":{"php":"7.4","wp":"5.3"},"steps":[{"progress":{"weight":3,"caption":"Logging in"},"step":"login","username":"admin","password":"password"}]}%
$jsonData = ( new BlueprintSerializer() )->toJson( $blueprint );
echo $jsonData . "\n";

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

print_r( $blueprint );
print_r( $resources );
die( 'xx' );

//try {
$importedBlueprint = ( new BlueprintDeserializer() )->fromJson( $jsonData );
print_r( $importedBlueprint );
//} catch ( \Swaggest\JsonSchema\Exception\LogicException $e ) {
//	// Nice and granular error information
//	print_r( [
//		"dataPointer"   => $e->getDataPointer(),
//		"schemaPointer" => $e->getSchemaPointer(),
//		// anyOf errors with {"step": "wp-cli"} only say it couldn't pattern match that
//		// to any of the steps. It should actually recognize it's the "wp-cli" step and
//		// provide a more specific error message about the missing properties.
//		// We may have to fork the validation library to patch that
//		"subErrors"     => $e->subErrors[0]->subErrors[23]->getSchemaPointer(),
//		"Data"          => $e->data,
//		"Constraint"    => $e->constraint,
//	] );
//}

// Setting invalid value (integer instead of string)
$builder->preferredVersions->php = 123;

// Exception: String expected, 123 received
//$jsonData = \WordPress\Blueprints\Model\Blueprint::export( $blueprint );

// Import valid json

$invalidJson = (object) [
	'landingPage'       => '',
	'preferredVersions' => (object) [
		'php' => '7.4',
		'wp'  => '6.4',
	],
	'steps'             => (object) [
		[
			'progress' => [
				'weight'  => 3,
				'caption' => "Logging in",
			],
			'step'     => "login",
			'username' => "admin",
			'password' => "password",
		],
	],
];

// Import invalid data
$invalidJson = (object) [
	'landingPage'       => '',
	'preferredVersions' => (object) [
		'php' => 123,
		'wp'  => 123,
	],
	'steps'             => (object) [
		[
			'progress' => [
				'weight'  => 3,
				'caption' => 123,
			],
			'step'     => 123,
			'username' => 123,
			'password' => 123,
		],
	],
];
try {
	$importedBlueprint = ( new BlueprintDeserializer() )->fromObject( $invalidJson );
} catch ( InvalidValue $e ) {
	// Nice and granular error information
	print_r( [
		"dataPointer"   => $e->getDataPointer(),
		"schemaPointer" => $e->getSchemaPointer(),
		"Message"       => $e->getMessage(),
		"Data"          => $e->data,
		"Constraint"    => $e->constraint,
	] );
}

