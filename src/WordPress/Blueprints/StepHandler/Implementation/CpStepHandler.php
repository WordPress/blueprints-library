<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\CpStep;
use WordPress\Blueprints\StepHandler\BaseStepHandler;


class CpStepHandler extends BaseStepHandler {
	/**
	 * @param CpStep $input
	 */
	function execute( CpStep $input ) {
		// @TODO: Treat the input paths as relative path to the document root (unless it's absolute)
		$success = copy( $input->fromPath, $input->toPath );
		if ( ! $success ) {
			throw new \Exception( "Failed to copy file from {$input->fromPath} to {$input->toPath}" );
		}
	}
}
