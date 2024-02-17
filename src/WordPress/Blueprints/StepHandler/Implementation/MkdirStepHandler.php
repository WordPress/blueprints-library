<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\MkdirStep;
use WordPress\Blueprints\StepHandler\BaseStepHandler;


class MkdirStepHandler extends BaseStepHandler {

	function execute( MkdirStep $input ) {
		// @TODO: Treat $input->path as relative path to the document root (unless it's absolute)
		$success = mkdir( $input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to create directory at {$input->path}" );
		}
	}
}
