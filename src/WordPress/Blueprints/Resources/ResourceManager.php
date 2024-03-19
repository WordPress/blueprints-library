<?php

namespace WordPress\Blueprints\Resources;

use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\Compile\CompiledResource;
use WordPress\Blueprints\Resources\Resolver\ResourceResolverCollection;
use WordPress\Util\Map;

class ResourceManager {

	protected $fs;
	protected $map;
	protected $resource_resolvers;

	public function __construct(
		ResourceResolverCollection $resource_resolvers
	) {
		$this->resource_resolvers = $resource_resolvers;
		$this->fs                 = new Filesystem();
		$this->map                = new Map();
	}

	/**
	 * @param mixed[] $compiledResources
	 */
	public function enqueue( $compiledResources ) {
		foreach ( $compiledResources as $compiled ) {
			/** @var CompiledResource $compiled */

			$this->map[ $compiled->declaration ] = $this->resource_resolvers->stream(
				$compiled->resource,
				$compiled->progressTracker
			);
		}
	}

	public function getStream( $key ) {
		return $this->map[ $key ];
	}

	public function bufferToTemporaryFile( $resource, $callback, $suffix = null ) {
		$fp   = $this->getStream( $resource );
		$path = $this->fs->tempnam( sys_get_temp_dir(), 'resource', $suffix );
		$this->fs->dumpFile( $path, $fp );

		try {
			return $callback( $path );
		} finally {
			$this->fs->remove( $path );
		}
	}
}
