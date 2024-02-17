<?php
/**
 * @file
 */

namespace WordPress\Blueprints\StepHandler;

use WordPress\Blueprints\Model\DataClass\MkdirStep;


class MkdirStepHandler extends BaseStepHandler {
	/**
	 * @param MkdirStep $input
	 */
	function execute( MkdirStep $input ) {
		$success = mkdir( $input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to create directory at {$input->path}" );
		}
	}
}
