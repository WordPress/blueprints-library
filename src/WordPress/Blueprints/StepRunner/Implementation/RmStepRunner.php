<?php

namespace WordPress\Blueprints\StepRunner\Implementation;

use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\StepRunner\BaseStepRunner;


class RmStepRunner extends BaseStepRunner {
	/**
	 * @param RmStep $input
	 */
	function run( RmStep $input ) {
		// @TODO: Treat $input->path as relative path to the document root (unless it's absolute)
		$success = unlink( $input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to remove the file at {$input->path}" );
		}
	}
}
