<?php

namespace WordPress\Blueprints\StepHandler;

use WordPress\Blueprints\Model\DataClass\CpStep;


class CpStepHandler extends BaseStepHandler {
	/**
	 * @param CpStep $input
	 */
	function execute( CpStep $input ) {
		$success = copy( $this->input->fromPath, $this->input->toPath );
		if ( ! $success ) {
			throw new \Exception( "Failed to copy file from {$this->input->fromPath} to {$this->input->toPath}" );
		}
	}
}
