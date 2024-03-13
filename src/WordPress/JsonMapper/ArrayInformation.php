<?php

namespace WordPress\JsonMapper;

class ArrayInformation
{
	/** @var bool */
	private $isArray;
	/** @var int */
	private $dimensions;

	private function __construct(bool $isArray, int $dimensions)
	{
		$this->isArray = $isArray;
		$this->dimensions = $dimensions;
	}

	public static function notAnArray(): self
	{
		return new self(false, 0);
	}

	public static function singleDimension(): self
	{
		return new self(true, 1);
	}

	public static function multiDimension(int $dimension): self
	{
		return new self(true, $dimension);
	}
}
