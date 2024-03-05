<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\FilesystemResource;
use WordPress\Blueprints\Progress\Tracker;

class FilesystemResourceResolver implements ResourceResolverInterface {

	public function parseUrl( string $url ): ?ResourceDefinitionInterface {
		if ( ! str_starts_with( $url, 'file://' ) ) {
			return null;
		}

		return ( new FilesystemResource() )->setPath( $url );
	}

	static public function getResourceClass(): string {
		return FilesystemResource::class;
	}

	public function supports( ResourceDefinitionInterface $resource ): bool {
		return $resource instanceof FilesystemResource;
	}

	public function stream( ResourceDefinitionInterface $resource, Tracker $progress_tracker ) {
		if ( ! $this->supports( $resource ) ) {
			throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}

		$progress_tracker->finish();

		/** @var $resource FilesystemResource */
		return fopen( $resource->path, 'r' );
	}

}
