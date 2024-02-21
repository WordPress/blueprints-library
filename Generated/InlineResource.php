<?php

namespace WordPress\Blueprints\Generated;

class InlineResource implements ResourceDefinitionInterface
{
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
}
