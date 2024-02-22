<?php

namespace WordPress\Blueprints\Resource;

use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Resource\Resolver\ResourceResolverInterface;

class ResourceManager {

	protected Filesystem $fs;
	protected ResourceMap $map;

	public function __construct(
		protected ResourceResolverInterface $resourceResolver
	) {
		$this->fs = new Filesystem();
		$this->map = new ResourceMap();
	}

	public function enqueue( array $resourceDeclarations ) {
		foreach ( $resourceDeclarations as [$originalInput, $resource] ) {
			$this->map[ $originalInput ] = $this->resourceResolver->stream( $resource );
		}
	}

	public function getStream( $key ) {
		return $this->map[ $key ];
	}


	public function bufferToTemporaryFile( $resource, $callback, $suffix = null ) {
		$fp = $this->getStream( $resource );
		$path = $this->fs->tempnam( sys_get_temp_dir(), 'resource', $suffix );
		$this->fs->dumpFile( $path, $fp );

		try {
			return $callback( $path );
		} finally {
			$this->fs->remove( $path );
		}
	}

}
