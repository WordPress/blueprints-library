<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\MkdirStep;


class MkdirStepRunner extends BaseStepRunner {

	function run( MkdirStep $input ) {
		// @TODO: Treat $input->path as relative path to the document root (unless it's absolute)
		$success = mkdir( $input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to create directory at {$input->path}" );
		}
	}
}
