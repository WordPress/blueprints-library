<?php

namespace WordPress\Blueprints\Model\DataClass;

class UrlResource implements ResourceDefinitionInterface {

	const DISCRIMINATOR = 'url';

	/**
	 * Identifies the file resource as a URL
	 *
	 * @var string
	 */
	public $resource = 'url';

	/**
	 * The URL of the file
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Optional caption for displaying a progress message
	 *
	 * @var string
	 */
	public $caption;


	/**
	 * @param string $resource
	 */
	public function setResource( $resource ) {
		$this->resource = $resource;
		return $this;
	}


	/**
	 * @param string $url
	 */
	public function setUrl( $url ) {
		$this->url = $url;
		return $this;
	}


	/**
	 * @param string $caption
	 */
	public function setCaption( $caption ) {
		$this->caption = $caption;
		return $this;
	}
}
