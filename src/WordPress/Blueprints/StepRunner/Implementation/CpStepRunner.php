<?php

namespace WordPress\Blueprints\StepRunner\Implementation;

use WordPress\Blueprints\Model\DataClass\CpStep;
use WordPress\Blueprints\StepRunner\BaseStepRunner;


class CpStepRunner extends BaseStepRunner {
	/**
	 * @param CpStep $input
	 */
	function run( CpStep $input ) {
		// @TODO: Treat the input paths as relative path to the document root (unless it's absolute)
		$success = copy( $input->fromPath, $input->toPath );
		if ( ! $success ) {
			throw new \Exception( "Failed to copy file from {$input->fromPath} to {$input->toPath}" );
		}
	}
}
