<?php

namespace WordPress\Blueprints\Resource;

use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use WordPress\Blueprints\Resource\Resolver\ResourceResolverInterface;

class ResourceManager {

	protected Filesystem $fs;
	protected ResourceMap $map;
    protected ResourceResolverInterface $resourceResolver;

    public function __construct(
		ResourceResolverInterface $resourceResolver
	) {
        $this->resourceResolver = $resourceResolver;
        $this->fs  = new Filesystem();
		$this->map = new ResourceMap();
	}

	public function enqueue( array $resourceDeclarations ) {
		foreach ( $resourceDeclarations as $resource ) {
			$this->map[ $resource ] = $this->resourceResolver->stream( $resource );
		}
	}

	public function getStream( FileReferenceInterface $resource ) {
		return $this->map[ $resource ];
	}


	public function bufferToTemporaryFile( FileReferenceInterface $resource, $callback, $suffix = null ) {
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
