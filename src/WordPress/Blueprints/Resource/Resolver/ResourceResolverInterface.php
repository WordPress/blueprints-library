<?php

namespace WordPress\Blueprints\Resource\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;

interface ResourceResolverInterface {
	public function parseUrl( string $url ): ResourceDefinitionInterface|false;

	public function supports( ResourceDefinitionInterface $resource ): bool;

	static public function getResourceClass(): string;

	public function stream( ResourceDefinitionInterface $resource );
}
