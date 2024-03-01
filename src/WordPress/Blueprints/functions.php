<?php

namespace WordPress\Blueprints;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use WordPress\Blueprints\Progress\DoneEvent;
use WordPress\Blueprints\Progress\ProgressEvent;
use WordPress\Blueprints\Runtime\Runtime;

function run_blueprint( $json, $environment, $documentRoot = '/wordpress', $progressSubscriber = null ) {
	$c = ( new ContainerBuilder() )->build(
		$environment,
		new Runtime( $documentRoot )
	);

	$engine = $c['blueprint.engine'];

	/** @var $engine Engine */
	return $engine->runBlueprint( $json, $progressSubscriber );
}

function list_files( string $path, $omitDotFiles = false ): array {
	return array_values( array_filter( scandir( $path ), function ( $file ) use ( $omitDotFiles ) {
		if ( $omitDotFiles && $file[0] === '.' ) {
			return false;
		}

		return '.' !== $file && '..' !== $file;
	} ) );
}

function move_files_from_directory_to_directory( string $from, string $to ) {
	$files = scandir( $from );
	foreach ( $files as $file ) {
		if ( '.' === $file || '..' === $file ) {
			continue;
		}
		$fromPath = $from . '/' . $file;
		$toPath = $to . '/' . $file;
		$success = rename( $fromPath, $toPath );
		if ( ! $success ) {
			throw new IOException( "Failed to move the file from {$fromPath} at {$toPath}" );
		}
	}
}

function join_paths() {
	$paths = array();

	foreach ( func_get_args() as $arg ) {
		if ( $arg !== '' ) {
			$paths[] = $arg;
		}
	}

	return preg_replace( '#/+#', '/', join( '/', $paths ) );
}
