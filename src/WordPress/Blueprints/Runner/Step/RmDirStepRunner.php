<?php

namespace WordPress\Blueprints\Runner\Step;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmDirStep;
use function WordPress\Blueprints\StepRunner\setCaption;


class RmDirStepRunner extends BaseStepRunner {

	function run( RmDirStep $input ) {
		try {
			$fs = new Filesystem();
			$fs->remove( $input->path );
		} catch ( IOException $e ) {
			throw new BlueprintException( "Failed to remove the directory at {$input->path}", 0, $e );
		}
	}
}
