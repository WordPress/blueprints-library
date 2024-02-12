<?php

namespace blueprints\src\WordPress\Blueprints\Steps\Cp;

use blueprints\src\WordPress\Blueprints\Steps\BaseStepInput;

class CpStepInput extends BaseStepInput {
	public function __construct(
		public string $fromPath,
		public string $toPath
	) {
	}
}
