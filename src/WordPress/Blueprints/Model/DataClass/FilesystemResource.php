<?php

namespace WordPress\Blueprints\Model\DataClass;

class FilesystemResource implements ResourceDefinitionInterface
{
	const DISCRIMINATOR = 'filesystem';

	/**
	 * Identifies the file resource as Virtual File System (VFS)
	 * @var string
	 */
	public $resource = 'filesystem';

	/**
	 * The path to the file in the VFS
	 * @var string
	 */
	public $path;


	public function setResource(string $resource)
	{
		$this->resource = $resource;
		return $this;
	}


	public function setPath(string $path)
	{
		$this->path = $path;
		return $this;
	}
}
