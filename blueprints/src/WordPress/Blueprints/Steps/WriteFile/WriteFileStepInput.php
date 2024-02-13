<?php

namespace WordPress\Blueprints\Steps\WriteFile;

use WordPress\Blueprints\Resources\Resource;
use WordPress\Blueprints\Steps\BaseStepInput;

class WriteFileStepInput extends BaseStepInput {
	public $file;
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
