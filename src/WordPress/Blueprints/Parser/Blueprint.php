<?php

namespace WordPress\Blueprints\Parser;

use Symfony\Component\Validator\Constraints as Assert;
use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedCollection;
use WordPress\Blueprints\Parser\Annotation\ResourceType;

class Blueprint {

	// Runtime-specific property, we don't use it in the Blueprint library
	public $php;

	// Plugin-specific properties, we ignore those in the core library
	public $plugins;

	// Runtime-specific property, we don't use it in the Blueprint library
	public $onBoot;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public $wpVersion;

	/**
	 * @Assert\Valid()
	 * @DiscriminatedCollection(group="step", typeProperty="step")
	 */
	public $steps;

}
