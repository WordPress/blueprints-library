<?php

namespace WordPress\Blueprints\ResourceResolver;

use WordPress\Blueprints\Model\Builder\FilesystemResourceBuilder;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use WordPress\Blueprints\Model\DataClass\FilesystemResource;
use WordPress\Blueprints\Model\DataClass\InlineResource;
use WordPress\Blueprints\Model\DataClass\UrlResource;

class FilesystemResourceResolver implements ResourceResolverInterface {

	public function parseUrl( string $url ): FileReferenceInterface|false {
		if ( ! str_starts_with( $url, 'file://' ) ) {
			return false;
		}

		return ( new FilesystemResourceBuilder() )->setPath( $url );
	}

	static public function getResourceClass(): string {
		return FilesystemResource::class;
	}

	public function supports( FileReferenceInterface $resource ): bool {
		return $resource instanceof FilesystemResource;
	}

	public function stream( FileReferenceInterface $resource ) {
		if ( ! $this->supports( $resource ) ) {
			throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}

		/** @var $resource FilesystemResource */
		return fopen( $resource->path, 'r' );
	}

}
