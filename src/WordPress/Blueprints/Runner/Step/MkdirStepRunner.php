<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\MkdirStep;


class MkdirStepRunner extends BaseStepRunner {

	/**
  * @param \WordPress\Blueprints\Model\DataClass\MkdirStep $input
  */
 function run( $input ) {
		// @TODO: Treat $input->path as relative path to the document root (unless it's absolute)
		$success = mkdir( $input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to create directory at {$input->path}" );
		}
	}
}
