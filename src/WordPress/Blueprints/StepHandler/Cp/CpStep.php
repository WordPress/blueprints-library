<?php

namespace WordPress\Blueprints\StepHandler\Cp;

use WordPress\Blueprints\StepHandler\BaseStep;

class CpStep extends BaseStep {
	public function __construct(
		private CpStepInput $input
	) {
		parent::__construct();
	}

	public function execute() {
		$success = copy( $this->input->fromPath, $this->input->toPath );
		if ( ! $success ) {
			throw new \Exception( "Failed to copy file from {$this->input->fromPath} to {$this->input->toPath}" );
		}
	}
}
