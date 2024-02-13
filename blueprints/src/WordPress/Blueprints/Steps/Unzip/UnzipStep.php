<?php

namespace WordPress\Blueprints\Steps\Unzip;

use WordPress\Blueprints\Steps\BaseStep;
use function WordPress\Zip\zip_extract_to;

class UnzipStep extends BaseStep {

	public function __construct(
		public UnzipStepInput $input
	) {
		parent::__construct();
	}

	public static function getInputClass(): string {
		return UnzipStepInput::class;
	}

	public function execute() {
		zip_extract_to( $this->input->zipFile, $this->input->toPath );
	}
}
