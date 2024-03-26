<?php

namespace WordPress\Blueprints\Runner\Step;

use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\MkdirStep;


class MkdirStepRunner extends BaseStepRunner {

	/**
	 * @param MkdirStep $input
	 */
	function run( MkdirStep $input ) {
		$resolved_path = $this->getRuntime()->resolvePath( $input->path );
		$filesystem   = new Filesystem();
		if ( $filesystem->exists( $resolved_path ) ) {
			throw new BlueprintException( "Failed to create \"$resolved_path\": the directory exists." );
		}
		$filesystem->mkdir( $resolved_path );
	}
}
