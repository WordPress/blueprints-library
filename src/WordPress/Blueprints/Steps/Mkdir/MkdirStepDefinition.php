<?php

namespace WordPress\Blueprints\Steps\Mkdir;

use Symfony\Component\Validator\Constraints as Assert;
use WordPress\Blueprints\Parser\Annotation\StepDefinition;

/**
 * @StepDefinition(id="mkdir")
 */
class MkdirStepDefinition {
	public function __construct(
		/** @Assert\Type("string") */
		public $path = null
	) {
	}

}
