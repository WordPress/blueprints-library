<?php

namespace WordPress\Blueprints\Resource\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;

class ResourceResolverCollection implements ResourceResolverInterface {

	public function __construct(
		/** @var ResourceResolverInterface[] */
		protected $ResourceResolvers
	) {
	}

	static public function getResourceClass(): string {
		throw new \RuntimeException( 'Not implemented' );
	}

	public function parseUrl( string $url ): ResourceDefinitionInterface|false {
		foreach ( $this->ResourceResolvers as $handler ) {
			$resource = $handler->parseUrl( $url );
			if ( $resource ) {
				return $resource;
			}
		}

		return false;
	}

	public function supports( ResourceDefinitionInterface $resource ): bool {
		foreach ( $this->ResourceResolvers as $handler ) {
			if ( $handler->supports( $resource ) ) {
				return true;
			}
		}

		return false;
	}

	public function stream( ResourceDefinitionInterface $resource ) {
		foreach ( $this->ResourceResolvers as $handler ) {
			if ( $handler->supports( $resource ) ) {
				return $handler->stream( $resource );
			}
		}

		throw new \InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
	}

}
