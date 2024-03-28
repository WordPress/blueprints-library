<?php

namespace WordPress\Blueprints\Runner\Step;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmStep;

class RmStepRunner extends BaseStepRunner {

	/**
	 * @param RmStep $input
	 */
	public function run( RmStep $input ) {
		$resolved_path = $this->getRuntime()->resolvePath( $input->path );
		$filesystem   = new Filesystem();
		if ( false === $filesystem->exists( $resolved_path ) ) {
			throw new BlueprintException( "Failed to remove \"$resolved_path\": the directory or file does not exist." );
		}
		$filesystem->remove( $resolved_path );
	}
}
