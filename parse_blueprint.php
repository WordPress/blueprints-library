<?php

use WordPress\Blueprints\Dependency\ContainerBuilder;
use WordPress\Blueprints\Parser\ParsingException;


require 'vendor/autoload.php';

$raw_blueprint = json_encode( [
	"wpVersion" => "6.4",
	"steps"     => [
		[ "step" => "unzip", "zipFile" => [ "source" => "url", "url" => "https://wp.org" ], "toPath" => "test" ],
		[ "step" => "mkdir", "path" => "/wordpress", ],
	],
] );

$builder   = new ContainerBuilder();
$container = $builder->build( 'native' );

try {
	$blueprint = $container['blueprint.parser']->parse( $raw_blueprint );
	var_dump( $blueprint );
} catch ( ParsingException $e ) {
	foreach ( $e->getViolations() as $path => $violation ) {
		var_dump( $path . ' => ' . $violation );
	}
}
