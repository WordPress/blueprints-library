<?php

namespace WordPress\Blueprints\Parser\Annotation;

use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedClass;

/**
 * @Annotation
 */
class StepDefinition extends DiscriminatedClass {
	public $group = "step";
}
