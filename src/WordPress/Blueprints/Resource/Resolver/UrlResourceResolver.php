<?php

namespace WordPress\Blueprints\Resource\Resolver;

use WordPress\Blueprints\Model\Dirty\UrlResource;
use WordPress\DataSource\DataSourceInterface;

class UrlResourceResolver implements ResourceResolverInterface {

	public function __construct( protected DataSourceInterface $dataSource ) {
	}

	public function parseUrl( string $url ) {
		if ( ! str_starts_with( $url, 'http://' ) && ! str_starts_with( $url, 'https://' ) ) {
			return false;
		}

		return UrlResource::create()->setUrl( $url );
	}


	static public function getResourceClass(): string {
		return UrlResource::class;
	}

	public function supports( $resource ): bool {
		return $resource instanceof UrlResource;
	}

	public function stream( $resource ) {
		if ( ! $this->supports( $resource ) ) {
			throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
		}

		/** @var $resource UrlResource */
		return $this->dataSource->stream( $resource->url );
	}

}
