<?php

namespace WordPress\Blueprints\Model\DataClass;

class CorePluginResource implements ResourceDefinitionInterface {

	const DISCRIMINATOR = 'wordpress.org/plugins';

	/**
	 * Identifies the file resource as a WordPress Core plugin
	 *
	 * @var string
	 */
	public $resource = 'wordpress.org/plugins';

	/**
	 * The slug of the WordPress Core plugin
	 *
	 * @var string
	 */
	public $slug;


	/**
	 * @param string $resource
	 */
	public function setResource( $resource ) {
		$this->resource = $resource;
		return $this;
	}


	/**
	 * @param string $slug
	 */
	public function setSlug( $slug ) {
		$this->slug = $slug;
		return $this;
	}
}
