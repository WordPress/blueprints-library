<?php

namespace WordPress\Blueprints\Resources\Resolver;

use InvalidArgumentException;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\DataSource\DataSourceInterface;
use WordPress\DataSource\DataSourceProgressEvent;

class UrlResourceResolver implements ResourceResolverInterface {

	protected $data_source;

	public function __construct( DataSourceInterface $data_source ) {
		$this->data_source = $data_source;
	}

	/**
	 * @param string $url
	 */
	public function parseUrl( $url ) {
		if ( strncmp( $url, 'http://', strlen( 'http://' ) ) !== 0 && strncmp( $url, 'https://', strlen( 'https://' ) ) !== 0 ) {
			return null;
		}

		return ( new UrlResource() )->setUrl( $url );
	}


	public static function getResourceClass(): string {
		return UrlResource::class;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface $resource
	 */
	public function supports( $resource ): bool {
		return $resource instanceof UrlResource;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface $resource
	 * @param \WordPress\Blueprints\Progress\Tracker                            $progress_tracker
	 */
	public function stream( $resource, $progress_tracker ) {
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
