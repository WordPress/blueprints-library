<?php

namespace WordPress\Blueprints\Model\DataClass;

class FilesystemResource implements ResourceDefinitionInterface {

	const DISCRIMINATOR = 'filesystem';

	/**
	 * Identifies the file resource as Virtual File System (VFS)
	 *
	 * @var string
	 */
	public $resource = 'filesystem';

	/**
	 * The path to the file in the VFS
	 *
	 * @var string
	 */
	public $path;


	/**
	 * @param string $resource
	 */
	public function setResource( $resource ) {
		$this->resource = $resource;
		return $this;
	}


	/**
	 * @param string $path
	 */
	public function setPath( $path ) {
		$this->path = $path;
		return $this;
	}
}
