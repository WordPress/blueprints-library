<?php

namespace WordPress\Blueprints\Resources;

use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\Compile\CompiledResource;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Resources\Resolver\ResourceResolverCollection;
use WordPress\Streams\AsyncHttpClient;
use WordPress\Util\Map;

class ResourceManager {

	protected Filesystem $fs;
	protected Map $map;
	protected AsyncHttpClient $group;
	protected ResourceResolverCollection $resource_resolvers;

	public function __construct(
		ResourceResolverCollection $resource_resolvers
	) {
		$this->resource_resolvers = $resource_resolvers;
		$this->fs = new Filesystem();
		$this->map = new Map();
	}

	public function enqueue( array $compiledResources ) {
		$urlResources = [];
		$otherResources = [];

		foreach ( $compiledResources as $compiled ) {
			/** @var CompiledResource $compiled */
			if ( $compiled->resource instanceof UrlResource ) {
				$urlResources[] = $compiled;
			} else {
				$otherResources[] = $compiled;
			}
		}

		$this->enqueueUrlResources( $urlResources );
		$this->enqueueResources( $otherResources );
	}

	private function enqueueUrlResources( array $urlResources ) {

	}

	private function enqueueResources( array $resources ) {
		foreach ( $resources as $compiled ) {
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
