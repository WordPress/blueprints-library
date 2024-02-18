<?php

namespace WordPress\Blueprints\Resource\Resolver;

use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;

interface ResourceResolverInterface {
	public function parseUrl( string $url ): FileReferenceInterface|false;

	public function supports( FileReferenceInterface $resource ): bool;

	static public function getResourceClass(): string;

	public function stream( FileReferenceInterface $resource );
}
