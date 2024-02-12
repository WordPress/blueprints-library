<?php

namespace blueprints\src\WordPress\Blueprints\Steps\Mkdir;

use blueprints\src\WordPress\Blueprints\Steps\BaseStepInput;

class MkdirStepInput extends BaseStepInput {
	public function __construct(
		public string $path
	) {
	}
}
