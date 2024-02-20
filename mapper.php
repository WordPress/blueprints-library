<?php

// An experiment at mapping a JSON blueprint definition to Blueprint PHP models.
// There is no data validation or error handling in this example.
// To validate, we'd still need to use a JSON schema validator.

use \WordPress\Blueprints\Model\DataClass\Blueprint;
use \WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;
use \WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use \WordPress\Blueprints\Model\DataClass\FilesystemResource;
use \WordPress\Blueprints\Model\DataClass\InlineResource;
use \WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use \WordPress\Blueprints\Model\DataClass\RunPHPStep;
use \WordPress\Blueprints\Model\DataClass\StepInterface;
use \WordPress\Blueprints\Model\DataClass\UrlResource;
use \WordPress\Blueprints\Model\DataClass\WriteFileStep;

require 'vendor/autoload.php';
$jsonBlueprint = json_decode(
	<<<BLUEPRINT
	{
		"wpVersion": "https://wordpress.org/latest.zip",
		"constants": {
			"WP_DEBUG": true
		},
		"plugins": [
			"hello-dolly",
			{
				"type": "url",
				"url": "https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip"
			}
		],
		"steps": [
			{
				"step": "runPHP",
				"code": "<?php echo 'Hello, world!'; ?>"
			},
			{
				"step": "installPlugin",
				"pluginZipFile": {
					"type": "url",
					"url": "https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip"
				}
			},
			{
				"step": "installPlugin",
				"pluginZipFile": "https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip",
				"progress": {
					"caption": "Installing Hello Dolly"
				}
			}
		]
	}
	BLUEPRINT,
	false
);
$mapper = new JsonMapper();
$mapper->classMap['PluginDefinition'] = function ( $class, $jvalue ) {
	if ( is_string( $jvalue ) ) {
		return $jvalue;
	}
	// examine $class and $jvalue to figure out what class to use...
	var_dump( $class );

	return 'string';
};

$stepClasses = [
	RunPHPStep::class,
	WriteFileStep::class,
	InstallPluginStep::class,
	DefineWpConfigConstsStep::class,
];
$stepMap = [];
foreach ( $stepClasses as $stepClass ) {
	$stepMap[ $stepClass::SLUG ] = $stepClass;
}
$mapper->classMap[ StepInterface::class ] = function ( $class, $jvalue ) use ( $stepMap ) {
	if ( ! isset( $jvalue->step ) ) {
		throw new InvalidArgumentException( "Step must be defined" );
	}
	if ( ! isset( $stepMap[ $jvalue->step ] ) ) {
		throw new InvalidArgumentException( "Step {$jvalue->step} is not implemented" );
	}

	return $stepMap[ $jvalue->step ];
};

$resourceClasses = [
	FilesystemResource::class,
	InlineResource::class,
	UrlResource::class,
];

$resourceMap = [];
foreach ( $resourceClasses as $resourceClass ) {
	$resourceMap[ $resourceClass::SLUG ] = $resourceClass;
}
$mapper->classMap[ FileReferenceInterface::class ] = function ( $class, $jvalue ) use ( $resourceMap ) {
	if ( is_string( $jvalue ) ) {
		// @TODO: We're forced to provide a class here, and that class must accept a string in its constructor.
		//        This is quite restricting. Ideally this mapper function could just return the mappped instance.
		//        Hm, yeah.
		//        But should the builder API may support a URL syntax like this?
		//           setPluginZipFile( 'https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip' )
		//        In which case we'll need to transform the strings to URL Resources later on?
		//
		//        OR! Should it only support a syntax like this?
		//           setPluginZipFile( new UrlResource( 'https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip' ) )
		//        In which case we're good to use the UrlResource::class as below.
		return UrlResource::class;
	}
	if ( ! isset( $jvalue->type ) ) {
		throw new InvalidArgumentException( "Resource type must be defined" );
	}
	if ( ! isset( $resourceMap[ $jvalue->type ] ) ) {
		throw new InvalidArgumentException( "Resource type {$jvalue->type} is not implemented" );
	}

	return $resourceMap[ $jvalue->type ];
};

$mapped = $mapper->map( $jsonBlueprint, Blueprint::class );
print_r( $mapped );

