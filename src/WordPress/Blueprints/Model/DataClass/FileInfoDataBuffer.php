<?php

namespace WordPress\Blueprints\Model\DataClass;

class FileInfoDataBuffer {

	/** @var float */
	public $byteLength;


	/**
	 * @param float $byteLength
	 */
	public function setByteLength( $byteLength ) {
		$this->byteLength = $byteLength;
		return $this;
	}
}
