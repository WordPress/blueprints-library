<?php

namespace WordPress\Blueprints\Parsing\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BlueprintSteps extends Constraint {
	public $message = 'Your custom validation message';

}
