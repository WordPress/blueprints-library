<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmDirStep;
use WordPress\Blueprints\StepHandler\BaseStepHandler;
use function WordPress\Blueprints\StepHandler\setCaption;


class RmDirStepHandler extends BaseStepHandler {

	function execute( RmDirStep $input ) {
		try {
			$fs = new Filesystem();
			$fs->remove( $input->path );
		} catch ( IOException $e ) {
			throw new BlueprintException( "Failed to remove the directory at {$input->path}", 0, $e );
		}
	}
}
