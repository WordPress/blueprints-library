<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Progress\Tracker;

interface ResourceResolverInterface {
	public function parseUrl( string $url ): ResourceDefinitionInterface|false;

	public function supports( ResourceDefinitionInterface $resource ): bool;

	static public function getResourceClass(): string;

	public function stream( ResourceDefinitionInterface $resource, Tracker $progressTracker );
}
