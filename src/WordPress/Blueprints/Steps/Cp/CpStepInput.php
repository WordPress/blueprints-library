<?php

namespace WordPress\Blueprints\Steps\Cp;

use WordPress\Blueprints\Parser\Annotation\StepDefinition;

/**
 * @StepDefinition(id="cp")
 */
class CpStepInput {
	public function __construct(
		public string $fromPath,
		public string $toPath
	) {
	}
}
