<?php

namespace WordPress\Blueprints\ResourceResolver;

use WordPress\Blueprints\Model\Builder\InlineResourceBuilder;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use WordPress\Blueprints\Model\DataClass\FilesystemResource;
use WordPress\Blueprints\Model\DataClass\InlineResource;

class InlineResourceResolver implements ResourceResolverInterface {

	public function parseUrl( string $url ): FileReferenceInterface|false {
		// If url starts with "protocol://" then we assume it's not inline raw data
		if ( 0 !== preg_match( '#^[a-z_+]+://#', $url ) ) {
			return false;
		}

		return ( new InlineResourceBuilder() )->setContents( $url );
	}

	static public function getResourceClass(): string {
		return InlineResource::class;
	}

	public function supports( FileReferenceInterface $resource ): bool {
		return $resource instanceof InlineResource;
	}

	public function stream( FileReferenceInterface $resource ) {
		if ( ! $this->supports( $resource ) ) {
			throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}
		/** @var $resource InlineResource */
		$fp = fopen( "php://temp", 'r+' );
		fwrite( $fp, $resource->contents );
		rewind( $fp );

		return $fp;
	}

}
