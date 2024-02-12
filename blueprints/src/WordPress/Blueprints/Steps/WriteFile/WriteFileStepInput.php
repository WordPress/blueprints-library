<?php

namespace blueprints\src\WordPress\Blueprints\Steps\WriteFile;

use blueprints\src\WordPress\Blueprints\Resources\Resource;
use blueprints\src\WordPress\Blueprints\Steps\BaseStepInput;

class WriteFileStepInput extends BaseStepInput {
	public function __construct(
		public Resource $file,
		public string $toPath
	) {
	}
}
