<?php

namespace WordPress\Blueprints\Steps\WriteFile;

use WordPress\Blueprints\Resources\Resource;
use WordPress\Blueprints\Steps\BaseStepInput;

class WriteFileStepInput extends BaseStepInput {
	public function __construct(
		public Resource $file,
		public string $toPath
	) {
	}
}
