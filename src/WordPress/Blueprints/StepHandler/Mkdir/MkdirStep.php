<?php

namespace WordPress\Blueprints\StepHandler\Mkdir;

use WordPress\Blueprints\StepHandler\BaseStep;

class MkdirStep extends BaseStep {
	public function __construct(
		private MkdirStepInput $input
	) {
	}

	public function execute() {
		$success = mkdir( $this->input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to create directory at {$this->input->path}" );
		}
	}
}
