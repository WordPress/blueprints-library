<?php

namespace WordPress\Blueprints\StepHandler\Cp;

use WordPress\Blueprints\StepHandler\BaseStepInput;

class CpStepInput extends BaseStepInput {
	public function __construct(
		public string $fromPath,
		public string $toPath
	) {
	}
}
