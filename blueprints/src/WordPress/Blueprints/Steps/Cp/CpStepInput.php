<?php

namespace WordPress\Blueprints\Steps\Cp;

use WordPress\Blueprints\Steps\BaseStepInput;

class CpStepInput extends BaseStepInput {
	public function __construct(
		public string $fromPath,
		public string $toPath
	) {
	}
}
