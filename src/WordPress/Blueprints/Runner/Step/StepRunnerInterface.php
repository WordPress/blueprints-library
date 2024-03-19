<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Resources\ResourceManager;
use WordPress\Blueprints\Runtime\RuntimeInterface;

interface StepRunnerInterface {

	/**
	 * @param \WordPress\Blueprints\Resources\ResourceManager $map
	 */
	public function setResourceManager( $map );

	/**
	 * @param \WordPress\Blueprints\Runtime\RuntimeInterface $runtime
	 */
	public function setRuntime( $runtime );

	// @TODO: Document how this method isn't defined because
	// PHP doens't support covariant arguments
	// function run( StepInterface $input, Tracker $tracker );
}
