<?php

namespace WordPress\Blueprints\Model\DataClass;

class InlineResource implements ResourceDefinitionInterface
{
	const DISCRIMINATOR = 'inline';

	/**
	 * Identifies the file resource as an inline string
	 * @var string
	 */
	public $resource = 'inline';

	/**
	 * The contents of the file
	 * @var string
	 */
	public $contents;


	public function setResource(string $resource)
	{
		$this->resource = $resource;
		return $this;
	}


	public function setContents(string $contents)
	{
		$this->contents = $contents;
		return $this;
	}
}
