<?php

namespace WordPress\Blueprints\Steps\WriteFile;

use WordPress\Blueprints\Steps\BaseStep;

class WriteFileStep extends BaseStep {
	public function __construct(
		public WriteFileStepInput $input
	) {
	}

	public static function getInputClass(): string {
		return WriteFileStepInput::class;
	}
	
	public function execute() {
		$this->input->file->saveTo( $this->input->toPath );
	}

}
