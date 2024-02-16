<?php

namespace WordPress\Blueprints\Steps\Unzip;

use Symfony\Component\Validator\Constraints as Assert;
use WordPress\Blueprints\Parser\Annotation\StepDefinition;
use WordPress\Blueprints\Parser\Annotation\ResourceType;

/**
 * @StepDefinition(id="unzip")
 */
class UnzipStepInput {

	/**
	 * @ResourceType
	 * @Assert\NotNull
	 * @Assert\Valid
	 */
	public $zipFile;

	/**
	 * @Assert\Type("string")
	 * @Assert\NotBlank
	 * @Assert\NotNull
	 */
	public $toPath;

	/**
	 * @param $zipFile
	 * @param string $toPath
	 */
	public function __construct( $zipFile = null, $toPath = null ) {
		$this->zipFile = $zipFile;
		$this->toPath  = $toPath;
	}

}
