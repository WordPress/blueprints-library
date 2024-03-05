<?php

namespace WordPress\Blueprints\Resources\Resolver;

use InvalidArgumentException;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\DataSource\DataSourceInterface;
use WordPress\DataSource\DataSourceProgressEvent;

class UrlResourceResolver implements ResourceResolverInterface {

	protected DataSourceInterface $data_source;

	public function __construct( DataSourceInterface $data_source ) {
		$this->data_source = $data_source;
	}

	public function parseUrl( string $url ): ?ResourceDefinitionInterface {
		if ( ! str_starts_with( $url, 'http://' ) && ! str_starts_with( $url, 'https://' ) ) {
			return null;
		}

		return ( new UrlResource() )->setUrl( $url );
	}


	public static function getResourceClass(): string {
		return UrlResource::class;
	}

	public function supports( ResourceDefinitionInterface $resource ): bool {
		return $resource instanceof UrlResource;
	}

	public function stream( ResourceDefinitionInterface $resource, Tracker $progress_tracker ) {
		if ( ! $this->supports( $resource ) ) {
			throw new InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}

		$this->data_source->events->addListener(
			DataSourceProgressEvent::class,
			function ( DataSourceProgressEvent $progress ) use ( $progress_tracker, $resource ) {
				if ( $resource->url === $progress->url ) {
					// If we don't have totalBytes, we assume 5MB
					$totalBytes = $progress->totalBytes ?: 5 * 1024 * 1024;

					$progress_tracker->set(
						100 * $progress->downloadedBytes / $totalBytes
					);
				}
			}
		);

		/** @var $resource UrlResource */
		return $this->data_source->stream( $resource->url );
	}
}
