<?php

namespace WordPress\Blueprints\Resources;

use Symfony\Component\Validator\Constraints as Assert;
use WordPress\Blueprints\Parser\Annotation\ResourceDefinition;

/**
 * @ResourceDefinition(id="url")
 */
class URLResource {

	/**
	 * @Assert\Type("string")
	 * @Assert\URL
	 * @Assert\NotBlank
	 */
	public $url;

}
