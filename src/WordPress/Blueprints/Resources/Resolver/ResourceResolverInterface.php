<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Progress\Tracker;

interface ResourceResolverInterface {
	/**
	 * @param string $url
	 */
	public function parseUrl( $url );

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface $resource
	 */
	public function supports( $resource ): bool;

	public static function getResourceClass(): string;

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface $resource
	 * @param \WordPress\Blueprints\Progress\Tracker                            $progress_tracker
	 */
	public function stream( $resource, $progress_tracker );
}
