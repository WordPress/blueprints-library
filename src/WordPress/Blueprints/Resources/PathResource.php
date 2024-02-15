<?php

namespace WordPress\Blueprints\Resources;

use Symfony\Component\Validator\Constraints as Assert;

class PathResource extends ResourceDeclaration {

	/**
	 * @Assert\Type("string")
	 * @Assert\NotBlank
	 * @Assert\NotNull
	 */
	public $path;

}
