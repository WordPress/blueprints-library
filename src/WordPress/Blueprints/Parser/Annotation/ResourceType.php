<?php

namespace WordPress\Blueprints\Parser\Annotation;

use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedObject;

/**
 * @Annotation
 */
class ResourceType extends DiscriminatedObject {
	public $group = "resource";
	public $typeProperty = "source";
}
