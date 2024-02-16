<?php

namespace WordPress\Blueprints\Steps\WriteFile;

use Symfony\Component\Validator\Constraints as Assert;
use WordPress\Blueprints\Parser\Annotation\StepDefinition;

/**
 * @StepDefinition(id="writeFile")
 */
class WriteFileStepDefinition {

	/** @Assert\Type("resource") */
	public $file;
	/** @Assert\Type("string") */
	public string $toPath;

	/**
	 * @param $file
	 * @param string $toPath
	 */
	public function __construct( $file, string $toPath ) {
		$this->file   = $file;
		$this->toPath = $toPath;
	}


}
