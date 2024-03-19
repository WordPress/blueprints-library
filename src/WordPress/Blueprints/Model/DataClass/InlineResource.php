<?php

namespace WordPress\Blueprints\Model\DataClass;

class InlineResource implements ResourceDefinitionInterface {

	const DISCRIMINATOR = 'inline';

	/**
	 * Identifies the file resource as an inline string
	 *
	 * @var string
	 */
	public $resource = 'inline';

	/**
	 * The contents of the file
	 *
	 * @var string
	 */
	public $contents;


	/**
	 * @param string $resource
	 */
	public function setResource( $resource ) {
		$this->resource = $resource;
		return $this;
	}


	/**
	 * @param string $contents
	 */
	public function setContents( $contents ) {
		$this->contents = $contents;
		return $this;
	}
}
