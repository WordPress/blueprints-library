<?php

namespace WordPress\Blueprints\Resources;

use Symfony\Component\Validator\Constraints as Assert;
use WordPress\Blueprints\Parser\Annotation\ResourceDefinition;

/**
 * @ResourceDefinition(id="path")
 */
class PathResource {

	/**
	 * @Assert\Type("string")
	 * @Assert\NotBlank
	 * @Assert\NotNull
	 */
	public $path;

}
