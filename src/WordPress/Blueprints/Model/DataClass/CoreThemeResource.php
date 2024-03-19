<?php

namespace WordPress\Blueprints\Model\DataClass;

class CoreThemeResource implements ResourceDefinitionInterface {

	const DISCRIMINATOR = 'wordpress.org/themes';

	/**
	 * Identifies the file resource as a WordPress Core theme
	 *
	 * @var string
	 */
	public $resource = 'wordpress.org/themes';

	/**
	 * The slug of the WordPress Core theme
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
