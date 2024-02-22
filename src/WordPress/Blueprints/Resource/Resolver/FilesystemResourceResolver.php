<?php

namespace WordPress\Blueprints\Resource\Resolver;

use WordPress\Blueprints\Model\Builder\FilesystemResourceBuilder;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\FilesystemResource;

class FilesystemResourceResolver implements ResourceResolverInterface {

	public function parseUrl( string $url ): ResourceDefinitionInterface|false {
		if ( ! str_starts_with( $url, 'file://' ) ) {
			return false;
		}

		return ( new FilesystemResourceBuilder() )->setPath( $url );
	}

	static public function getResourceClass(): string {
		return FilesystemResource::class;
	}

	public function supports( ResourceDefinitionInterface $resource ): bool {
		return $resource instanceof FilesystemResource;
	}

	public function stream( ResourceDefinitionInterface $resource ) {
		if ( ! $this->supports( $resource ) ) {
			throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}

		/** @var $resource FilesystemResource */
		return fopen( $resource->path, 'r' );
	}

}
