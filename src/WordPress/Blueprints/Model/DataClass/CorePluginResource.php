<?php

namespace WordPress\Blueprints\Model\DataClass;

class CorePluginResource implements ResourceDefinitionInterface
{
	const DISCRIMINATOR = 'wordpress.org/plugins';

	/**
	 * Identifies the file resource as a WordPress Core plugin
	 * @var string
	 */
	public $resource = 'wordpress.org/plugins';

	/**
	 * The slug of the WordPress Core plugin
	 * @var string
	 */
	public $slug;


	public function setResource(string $resource)
	{
		$this->resource = $resource;
		return $this;
	}


	public function setSlug(string $slug)
	{
		$this->slug = $slug;
		return $this;
	}
}
