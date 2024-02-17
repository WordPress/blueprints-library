<?php
/**
 * @file
 */

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\MvStep;
use WordPress\Blueprints\StepHandler\BaseStepHandler;


class MvStepHandler extends BaseStepHandler {
	/**
	 * @param MvStep $input
	 */
	function execute( MvStep $input ) {
		// @TODO: Treat these paths as relative path to the document root (unless it's absolute)
		$success = rename( $input->fromPath, $input->toPath );
		if ( ! $success ) {
			throw new \Exception( "Failed to move the file from {$input->fromPath} at {$input->toPath}" );
		}
	}
}
