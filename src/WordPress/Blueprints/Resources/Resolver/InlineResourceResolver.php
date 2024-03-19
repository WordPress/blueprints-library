<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\Builder\InlineResourceBuilder;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\InlineResource;
use WordPress\Blueprints\Progress\Tracker;

class InlineResourceResolver implements ResourceResolverInterface {

	/**
	 * @param string $url
	 */
	public function parseUrl( $url ) {
		// If url starts with "protocol://" then we assume it's not inline raw data
		if ( 0 !== preg_match( '#^[a-z_+]+://#', $url ) ) {
			return null;
		}

		return ( new InlineResource() )->setContents( $url );
	}

	public static function getResourceClass(): string {
		return InlineResource::class;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface $resource
	 */
	public function supports( $resource ): bool {
		return $resource instanceof InlineResource;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface $resource
	 * @param \WordPress\Blueprints\Progress\Tracker                            $progress_tracker
	 */
	public function stream( $resource, $progress_tracker ) {
		if ( ! $this->supports( $resource ) ) {
			throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}
		$progress_tracker->finish();

		/** @var $resource InlineResource */
		$fp = fopen( 'php://temp', 'r+' );
		fwrite( $fp, $resource->contents );
		rewind( $fp );

		return $fp;
	}
}
