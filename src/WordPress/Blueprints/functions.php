<?php

namespace WordPress\Blueprints;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Filesystem\Exception\IOException;
use WordPress\Blueprints\Runtime\Runtime;

function run_blueprint( $json, $options = array() ) {

	$environment        = $options['environment'] ?? ContainerBuilder::ENVIRONMENT_NATIVE;
	$documentRoot       = $options['documentRoot'] ?? '/wordpress';
	$progressSubscriber = $options['progressSubscriber'] ?? null;
	$progressType       = $options['progressType'] ?? 'all';

	$c = ( new ContainerBuilder() )->build(
		$environment,
		new Runtime( $documentRoot )
	);
	/** @var $engine Engine */
	$engine            = $c['blueprint.engine'];
	$compiledBlueprint = $engine->parseAndCompile( $json );

	if ( $progressSubscriber ) {
		if ( $progressType === 'steps' ) {
			$compiledBlueprint->stepsProgressStage->events->addSubscriber( $progressSubscriber );
		} else {
			$compiledBlueprint->progressTracker->events->addSubscriber( $progressSubscriber );
		}
	}

	return $engine->run( $compiledBlueprint );
}

function list_files( string $path, $omitDotFiles = false ): array {
	return array_values(
		array_filter(
			scandir( $path ),
			function ( $file ) use ( $omitDotFiles ) {
				if ( $omitDotFiles && $file[0] === '.' ) {
					return false;
				}

				return '.' !== $file && '..' !== $file;
			}
		)
	);
}

function move_files_from_directory_to_directory( string $from, string $to ) {
	$fs    = new Filesystem();
	$files = scandir( $from );
	foreach ( $files as $file ) {
		if ( '.' === $file || '..' === $file ) {
			continue;
		}
		$fromPath = Path::canonicalize( $from . '/' . $file );
		$toPath   = Path::canonicalize( $to . '/' . $file );
		try {
			$fs->rename( $fromPath, $toPath );
		} catch ( IOException $exception ) {
			throw new BlueprintException( "Failed to move the file from {$fromPath} at {$toPath}", 0, $exception );
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
