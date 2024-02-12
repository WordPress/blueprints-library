<?php

namespace WordPress\Blueprints\Steps\WriteFile;

use WordPress\Blueprints\Steps\BaseStepInput;
use WordPress\Blueprints\Resources\Resource;

class WriteFileStepInput extends BaseStepInput {
	public function __construct(
		public Resource $file,
		public string $toPath
	) {
	}
}
