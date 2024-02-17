<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\StepHandler\BaseStepHandler;


class RmStepHandler extends BaseStepHandler {
	/**
	 * @param RmStep $input
	 */
	function execute( RmStep $input ) {
		// @TODO: Treat $input->path as relative path to the document root (unless it's absolute)
		$success = unlink( $input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to remove the file at {$input->path}" );
		}
	}
}
