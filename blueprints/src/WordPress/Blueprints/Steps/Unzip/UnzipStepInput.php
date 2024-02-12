<?php


namespace WordPress\Blueprints\Steps\Unzip;

use WordPress\Blueprints\Steps\BaseStepInput;

class UnzipStepInput extends BaseStepInput {
	public function __construct(
		public $zipFile,
		public string $toPath
	) {
	}
}
