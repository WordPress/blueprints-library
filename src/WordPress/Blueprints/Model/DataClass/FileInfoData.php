<?php

namespace WordPress\Blueprints\Model\DataClass;

class FileInfoData {

	/** @var float */
	public $BYTES_PER_ELEMENT;

	/** @var FileInfoDataBuffer */
	public $buffer;

	/** @var float */
	public $byteLength;

	/** @var float */
	public $byteOffset;

	/** @var float */
	public $length;


	/**
	 * @param float $BYTES_PER_ELEMENT
	 */
	public function setBYTES_PER_ELEMENT( $BYTES_PER_ELEMENT ) {
		$this->BYTES_PER_ELEMENT = $BYTES_PER_ELEMENT;
		return $this;
	}


	/**
	 * @param \WordPress\Blueprints\Model\DataClass\FileInfoDataBuffer $buffer
	 */
	public function setBuffer( $buffer ) {
		$this->buffer = $buffer;
		return $this;
	}


	/**
	 * @param float $byteLength
	 */
	public function setByteLength( $byteLength ) {
		$this->byteLength = $byteLength;
		return $this;
	}


	/**
	 * @param float $byteOffset
	 */
	public function setByteOffset( $byteOffset ) {
		$this->byteOffset = $byteOffset;
		return $this;
	}


	/**
	 * @param float $length
	 */
	public function setLength( $length ) {
		$this->length = $length;
		return $this;
	}
}
