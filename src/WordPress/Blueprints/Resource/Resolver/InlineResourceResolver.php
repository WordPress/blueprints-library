<?php

namespace WordPress\Blueprints\Resource\Resolver;

use WordPress\Blueprints\Model\Builder\InlineResourceBuilder;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\InlineResource;

class InlineResourceResolver implements ResourceResolverInterface {

	public function parseUrl( string $url ): ResourceDefinitionInterface|false {
		// If url starts with "protocol://" then we assume it's not inline raw data
		if ( 0 !== preg_match( '#^[a-z_+]+://#', $url ) ) {
			return false;
		}

		return ( new InlineResourceBuilder() )->setContents( $url );
	}

	static public function getResourceClass(): string {
		return InlineResource::class;
	}

	public function supports( ResourceDefinitionInterface $resource ): bool {
		return $resource instanceof InlineResource;
	}

	public function stream( ResourceDefinitionInterface $resource ) {
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
