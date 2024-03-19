<?php

namespace WordPress\Blueprints\Model\DataClass;

class FileInfoDataBuffer
{
	/** @var float */
	public $byteLength;


	public function setByteLength(float $byteLength)
	{
		$this->byteLength = $byteLength;
		return $this;
	}
}
