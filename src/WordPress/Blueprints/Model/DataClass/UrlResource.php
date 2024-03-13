<?php

namespace WordPress\Blueprints\Model\DataClass;

class UrlResource implements ResourceDefinitionInterface {
	public const DISCRIMINATOR = 'url';

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


	public function setResource( string $resource ) {
		$this->resource = $resource;

		return $this;
	}


	public function setUrl( string $url ) {
		// @TODO mapper supposedly sets values with setters, so why does this not set the caption?
		$this->url = $url;
//		if ( ! $this->caption ) {
//			$this->caption = 'Downloading ' . $url;
//		}

		return $this;
	}


	public function setCaption( string $caption ) {
		$this->caption = $caption;

		return $this;
	}
}
