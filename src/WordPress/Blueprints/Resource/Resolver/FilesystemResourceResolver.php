<?php

namespace WordPress\Blueprints\Resource\Resolver;

use WordPress\Blueprints\Model\Dirty\FilesystemResource;

class FilesystemResourceResolver implements ResourceResolverInterface {

	public function parseUrl( string $url ) {
		if ( ! str_starts_with( $url, 'file://' ) ) {
			return false;
		}

		return FilesystemResource::create()->setPath( $url );
	}

	static public function getResourceClass(): string {
		return FilesystemResource::class;
	}

	public function supports( $resource ): bool {
		return $resource instanceof FilesystemResource;
	}

	public function stream( $resource ) {
		if ( ! $this->supports( $resource ) ) {
			throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}

		/** @var $resource FilesystemResource */
		return fopen( $resource->path, 'r' );
	}

}
