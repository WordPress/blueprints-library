<?php


namespace blueprints\src\WordPress\Blueprints\Steps\Unzip;

use blueprints\src\WordPress\Blueprints\Resources\Resource;
use blueprints\src\WordPress\Blueprints\Steps\BaseStepInput;

class UnzipStepInput extends BaseStepInput {
	public function __construct(
		public Resource $zipFile,
		public string $toPath
	) {
	}
}
