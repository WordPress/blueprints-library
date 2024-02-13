<?php

namespace WordPress\Blueprints\Steps\Unzip;

class UnzipStepInput {
	public $zipFile;
	public string $toPath;

	/**
	 * @param $zipFile
	 * @param string $toPath
	 */
	public function __construct( $zipFile, string $toPath ) {
		$this->zipFile = $zipFile;
		$this->toPath  = $toPath;
	}

}
