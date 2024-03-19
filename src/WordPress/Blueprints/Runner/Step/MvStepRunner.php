<?php
/**
 * @file
 */

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\MvStep;


class MvStepRunner extends BaseStepRunner {
	/**
	 * @param MvStep $input
	 */
	function run( $input ) {
		// @TODO: Treat these paths as relative path to the document root (unless it's absolute)
		$success = rename( $input->fromPath, $input->toPath );
		if ( ! $success ) {
			throw new \Exception( "Failed to move the file from {$input->fromPath} at {$input->toPath}" );
		}
	}
}
