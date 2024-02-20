<?php

namespace WordPress\Blueprints\Resource\Resolver;

use WordPress\Blueprints\Model\Dirty\InlineResource;
use WordPress\Blueprints\Model\InternalValidated\ValidInlineResource;

class InlineResourceResolver implements ResourceResolverInterface {

	public function parseUrl( string $url ) {
		// If url starts with "protocol://" then we assume it's not inline raw data
		if ( 0 !== preg_match( '#^[a-z_+]+://#', $url ) ) {
			return false;
		}

		return InlineResource::create()->setContents( $url );
	}

	static public function getResourceClass(): string {
		return InlineResource::class;
	}

	public function supports( $resource ): bool {
		return $resource instanceof InlineResource;
	}

	public function stream( $resource ) {
		if ( ! $this->supports( $resource ) ) {
			throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}
		/** @var $resource ValidInlineResource */
		$fp = fopen( "php://temp", 'r+' );
		fwrite( $fp, $resource->contents );
		rewind( $fp );

		return $fp;
	}

}
