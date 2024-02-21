<?php

use Symfony\Component\Validator\Validation;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Generated\Model\Blueprint;
use WordPress\Blueprints\Generated\Normalizer\BlueprintNormalizer;
use WordPress\Blueprints\Generated\Validator\BlueprintConstraint;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintPreferredVersionsBuilder;
use WordPress\Blueprints\Model\Builder\InlineResourceBuilder;
use WordPress\Blueprints\Model\Builder\WriteFileStepBuilder;
use WordPress\Blueprints\Runtime\NativePHPRuntime;

require 'vendor/autoload.php';

$blueprintJson = <<<'BLUEPRINT'
{
	"constants": {
		"WP_DEBUG": true
	},
	"plugins": [
		"akismet"
	],
	"steps": [

	]
}
BLUEPRINT;


//$normalizer = new BlueprintNormalizer();
//$model = $normalizer->denormalize( json_decode( $blueprintJson, true ), Blueprint::class );
//var_dump( $model );

$normalizers = [
	new \Symfony\Component\Serializer\Normalizer\ArrayDenormalizer(),
	new \WordPress\Blueprints\Generated\Normalizer\JaneObjectNormalizer(),
];

$serializer = new \Symfony\Component\Serializer\Serializer(
	$normalizers,
	[ new \Symfony\Component\Serializer\Encoder\JsonEncoder() ]
);

try {
	$result = $serializer->deserialize(
		$blueprintJson,
		Blueprint::class,
		'json'
	);
} catch ( \WordPress\Blueprints\Generated\Runtime\Normalizer\ValidationException $e ) {
	foreach ( $e->getViolationList() as $violation ) {
		var_dump( $violation->getPropertyPath() . ': ' . $violation->getMessage() );
	}
}

print_r( $result );

//$validator = Validation::createValidator();
//$violations = $validator->validate(
//	$result,
//	new BlueprintConstraint()
//);
//foreach ( $violations as $k => $violation ) {
//	var_dump( $violation->getPropertyPath() . ': ' . $violation->getMessage() );
//}

//var_dump( $result );
