<?php

namespace WordPress\Blueprints\Resource\Resolver;


interface ResourceResolverInterface {
	public function parseUrl( string $url );

	public function supports( $resource ): bool;

	static public function getResourceClass(): string;

	public function stream( $resource );
}
