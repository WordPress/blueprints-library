<?php

namespace WordPress\Blueprints\StepHandler\Mkdir;

use WordPress\Blueprints\StepHandler\BaseStepInput;

class MkdirStepInput extends BaseStepInput {
	public function __construct(
		public string $path
	) {
	}
}
