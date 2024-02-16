<?php

use Swaggest\JsonSchema\InvalidValue;
use WordPress\Blueprints\Model\BlueprintDeserializer;
use WordPress\Blueprints\Model\BlueprintSerializer;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintPreferredVersionsBuilder;
use WordPress\Blueprints\Model\Builder\LoginStepBuilder;
use WordPress\Blueprints\Model\Builder\ProgressBuilder;

require 'vendor/autoload.php';

$blueprint = new BlueprintBuilder();
$blueprint
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
	] );

// No exception on exporting valid data
// {"landingPage":"\/wp-admin","preferredVersions":{"php":"7.4","wp":"5.3"},"steps":[{"progress":{"weight":3,"caption":"Logging in"},"step":"login","username":"admin","password":"password"}]}%
$jsonData = ( new BlueprintSerializer() )->toJson( $blueprint );
echo $jsonData . "\n";

$importedBlueprint = ( new BlueprintDeserializer() )->fromJson( $jsonData );
print_r( $importedBlueprint );

// Setting invalid value (integer instead of string)
$blueprint->preferredVersions->php = 123;

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

