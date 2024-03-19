<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\CpStep;


class CpStepRunner extends BaseStepRunner {
	/**
	 * @param CpStep $input
	 */
	function run( $input ) {
		// @TODO: Treat the input paths as relative path to the document root (unless it's absolute)
		$success = copy( $input->fromPath, $input->toPath );
		if ( ! $success ) {
			throw new \Exception( "Failed to copy file from {$input->fromPath} to {$input->toPath}" );
		}
	}
}
