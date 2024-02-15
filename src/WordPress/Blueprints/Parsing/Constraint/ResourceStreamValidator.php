<?php

namespace WordPress\Blueprints\Parsing\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ResourceStreamValidator extends ConstraintValidator {
	public function validate( $value, Constraint $constraint ) {
		// Noop validator, just so we can take advantage of the annotation parsing
		// performed by Symfony Validators.
	}
}
