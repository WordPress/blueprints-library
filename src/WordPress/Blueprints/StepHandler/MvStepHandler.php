<?php
/**
 * @file
 */

namespace WordPress\Blueprints\StepHandler;

use WordPress\Blueprints\Model\DataClass\MvStep;


class MvStepHandler extends BaseStepHandler {
	/**
	 * @param MvStep $input
	 */
	function execute( MvStep $input ) {
		$success = rename( $input->fromPath, $input->toPath );
		if ( ! $success ) {
			throw new \Exception( "Failed to move the file from {$input->fromPath} at {$input->toPath}" );
		}
	}
}
