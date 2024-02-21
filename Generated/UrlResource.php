<?php

namespace WordPress\Blueprints\Generated;

class UrlResource implements ResourceDefinitionInterface
{
	/**
	 * Identifies the file resource as a URL
	 * @var string
	 */
	public $resource = 'url';

	/**
	 * The URL of the file
	 * @var string
	 */
	public $url;

	/**
	 * Optional caption for displaying a progress message
	 * @var string
	 */
	public $caption;
}
