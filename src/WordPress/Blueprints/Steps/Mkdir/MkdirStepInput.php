<?php

namespace WordPress\Blueprints\Steps\Mkdir;

use Symfony\Component\Validator\Constraints as Assert;

class MkdirStepInput {
	public function __construct(
		/** @Assert\Type("string") */
		public $path = null
	) {
	}

}
