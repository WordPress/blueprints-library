<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\FilesystemResource;
use WordPress\Blueprints\Progress\Tracker;

class FilesystemResourceResolver implements ResourceResolverInterface {

	/**
	 * @param string $url
	 */
	public function parseUrl( $url ) {
		if ( strncmp( $url, 'file://', strlen( 'file://' ) ) !== 0 ) {
			return null;
		}

		return ( new FilesystemResource() )->setPath( $url );
	}

	public static function getResourceClass(): string {
		return FilesystemResource::class;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface $resource
	 */
	public function supports( $resource ): bool {
		return $resource instanceof FilesystemResource;
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

		/** @var $resource FilesystemResource */
		return fopen( $resource->path, 'r' );
	}
}
