<?php

namespace blueprints\src\WordPress\Blueprints\Steps\WriteFile;

use blueprints\src\WordPress\Blueprints\Steps\BaseStep;

class WriteFileStep extends BaseStep {
	public function __construct(
		public WriteFileStepInput $input
	) {
	}

	public function execute() {
		$this->input->file->saveTo( $this->input->toPath );
	}
}
