<?php

namespace WordPress\Blueprints\Generated;

class FilesystemResource implements ResourceDefinitionInterface
{
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
}
