<?php

namespace WordPress\Blueprints\StepHandler;

use WordPress\Blueprints\Model\DataClass\RmStep;


class RmStepHandler extends BaseStepHandler {
	/**
	 * @param RmStep $input
	 */
	function execute( RmStep $input ) {
		$success = unlink( $input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to remove the file at {$input->path}" );
		}
	}
}
