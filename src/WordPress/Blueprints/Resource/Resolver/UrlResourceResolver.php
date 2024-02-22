<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\Builder\UrlResourceBuilder;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\DataSource\DataSourceInterface;
use WordPress\DataSource\DataSourceProgressEvent;

class UrlResourceResolver implements ResourceResolverInterface {

	public function __construct( protected DataSourceInterface $dataSource ) {
	}

	public function parseUrl( string $url ): ResourceDefinitionInterface|false {
		if ( ! str_starts_with( $url, 'http://' ) && ! str_starts_with( $url, 'https://' ) ) {
			return false;
		}

		return ( new UrlResource() )->setUrl( $url );
	}


	static public function getResourceClass(): string {
		return UrlResource::class;
	}

	public function supports( ResourceDefinitionInterface $resource ): bool {
		return $resource instanceof UrlResource;
	}

	public function stream( ResourceDefinitionInterface $resource, Tracker $progressTracker ) {
		if ( ! $this->supports( $resource ) ) {
			throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}

		$this->dataSource->events->addListener( DataSourceProgressEvent::class,
			function ( DataSourceProgressEvent $progress ) use ( $progressTracker, $resource ) {
				if ( $resource->url === $progress->url ) {
					// If we don't have totalBytes, we assume 5MB
					$totalBytes = $progress->totalBytes ?: 5 * 1024 * 1024;

					$progressTracker->set(
						100 * $progress->downloadedBytes / $totalBytes
					);
				}
			} );

		/** @var $resource UrlResource */
		return $this->dataSource->stream( $resource->url );
	}

}
