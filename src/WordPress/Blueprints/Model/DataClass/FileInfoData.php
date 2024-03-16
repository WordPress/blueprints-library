<?php

namespace WordPress\Blueprints\Model\DataClass;

class FileInfoData
{
	/** @var float */
	public $BYTES_PER_ELEMENT = null;

	/** @var FileInfoDataBuffer */
	public $buffer = null;

	/** @var float */
	public $byteLength = null;

	/** @var float */
	public $byteOffset = null;

	/** @var float */
	public $length = null;


	public function setBYTES_PER_ELEMENT(float $BYTES_PER_ELEMENT)
	{
		$this->BYTES_PER_ELEMENT = $BYTES_PER_ELEMENT;
		return $this;
	}


	public function setBuffer(FileInfoDataBuffer $buffer)
	{
		$this->buffer = $buffer;
		return $this;
	}


	public function setByteLength(float $byteLength)
	{
		$this->byteLength = $byteLength;
		return $this;
	}


	public function setByteOffset(float $byteOffset)
	{
		$this->byteOffset = $byteOffset;
		return $this;
	}


	public function setLength(float $length)
	{
		$this->length = $length;
		return $this;
	}
}
