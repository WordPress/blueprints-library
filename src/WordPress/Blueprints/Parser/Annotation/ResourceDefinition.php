<?php

namespace WordPress\Blueprints\Parser\Annotation;

use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedClass;

/**
 * @Annotation
 */
class ResourceDefinition extends DiscriminatedClass {
	public $group = "resource";
}
