<?php

namespace WordPress\Blueprints\Model\DataClass;

class FileInfoDataBuffer
{
	/** @var float */
	public $byteLength = null;


	public function setByteLength(float $byteLength)
	{
		$this->byteLength = $byteLength;
		return $this;
	}
}
