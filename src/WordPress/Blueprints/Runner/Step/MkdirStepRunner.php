<?php

namespace WordPress\Blueprints\Runner\Step;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\MkdirStep;


class MkdirStepRunner extends BaseStepRunner {

	/**
	 * @param MkdirStep $input
	 */
	function run( MkdirStep $input ) {
		$resolvedPath = $this->getRuntime()->resolvePath( $input->path );
		$fileSystem   = new Filesystem();
		try {
			$fileSystem->mkdir( $resolvedPath );
		} catch ( IOException $exception ) {
			throw new BlueprintException( "Failed to create a directory at \"$resolvedPath\"", 0, $exception );
		}
	}
}
