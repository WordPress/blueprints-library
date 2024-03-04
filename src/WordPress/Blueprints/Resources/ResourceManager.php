<?php

namespace WordPress\Blueprints\Resources;

use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\Compile\CompiledResource;
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

	public function enqueue( array $compiledResources ) {
		foreach ( $compiledResources as $compiled ) {
			/** @var CompiledResource $compiled */
			$this->map[ $compiled->declaration ] = $this->resourceResolver->stream(
				$compiled->resource,
				$compiled->progressTracker
			);
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
