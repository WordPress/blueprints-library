<?php

namespace WordPress\Blueprints\ResourceResolver;

use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use WordPress\Blueprints\Model\DataClass\InlineResource;

class ResourceResolverCollection implements ResourceResolverInterface {

	public function __construct(
		/** @var ResourceResolverInterface[] */
		protected $ResourceResolvers
	) {
	}

	static public function getResourceClass(): string {
		throw new \RuntimeException( 'Not implemented' );
	}

	public function parseUrl( string $url ): FileReferenceInterface|false {
		foreach ( $this->ResourceResolvers as $handler ) {
			$resource = $handler->parseUrl( $url );
			if ( $resource ) {
				return $resource;
			}
		}

		return false;
	}

	public function supports( FileReferenceInterface $resource ): bool {
		foreach ( $this->ResourceResolvers as $handler ) {
			if ( $handler->supports( $resource ) ) {
				return true;
			}
		}

		return false;
	}

	public function stream( FileReferenceInterface $resource ) {
		foreach ( $this->ResourceResolvers as $handler ) {
			if ( $handler->supports( $resource ) ) {
				return $handler->stream( $resource );
			}
		}

		throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
	}

}
