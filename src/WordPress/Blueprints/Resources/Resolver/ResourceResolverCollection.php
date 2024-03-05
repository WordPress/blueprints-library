<?php

namespace WordPress\Blueprints\Resources\Resolver;

use InvalidArgumentException;
use RuntimeException;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Progress\Tracker;

class ResourceResolverCollection implements ResourceResolverInterface {

	/** @var ResourceResolverInterface[] */
	protected array $resource_resolvers;

	public function __construct(
		array $resource_resolvers
	) {
		$this->resource_resolvers = $resource_resolvers;
	}

	public static function getResourceClass(): string {
		throw new RuntimeException( 'Not implemented' );
	}

	public function parseUrl( string $url ): ?ResourceDefinitionInterface {
		foreach ( $this->resource_resolvers as $resolver ) {
			/** @var ResourceResolverInterface $resolver */
			$resource = $resolver->parseUrl( $url );
			if ( $resource ) {
				return $resource;
			}
		}

		return null;
	}

	public function supports( ResourceDefinitionInterface $resource ): bool {
		foreach ( $this->resource_resolvers as $resolver ) {
			/** @var ResourceResolverInterface $resolver */
			if ( $resolver->supports( $resource ) ) {
				return true;
			}
		}

		return false;
	}

	public function stream( ResourceDefinitionInterface $resource, Tracker $progressTracker ) {
		foreach ( $this->resource_resolvers as $resolver ) {
			/** @var ResourceResolverInterface $resolver */
			if ( $resolver->supports( $resource ) ) {
				return $resolver->stream( $resource, $progressTracker );
			}
		}

		throw new InvalidArgumentException( 'Resource ' . get_class( $resource ) . ' unsupported' );
	}
}
