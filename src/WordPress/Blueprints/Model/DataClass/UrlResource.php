<?php

namespace WordPress\Blueprints\Model\DataClass;

class UrlResource implements ResourceDefinitionInterface
{
	const DISCRIMINATOR = 'url';

	/**
	 * Identifies the file resource as a URL
	 * @var string
	 */
	public $resource = 'url';

	/**
	 * The URL of the file
	 * @var string
	 */
	public $url = null;

	/**
	 * Optional caption for displaying a progress message
	 * @var string
	 */
	public $caption = null;


	public function setResource(string $resource)
	{
		$this->resource = $resource;
		return $this;
	}


	public function setUrl(string $url)
	{
		$this->url = $url;
		return $this;
	}


	public function setCaption(string $caption)
	{
		$this->caption = $caption;
		return $this;
	}
}
