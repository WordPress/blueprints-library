<?php

namespace WordPress\Blueprints\StepRunner;

use WordPress\Blueprints\ResourceManager;
use WordPress\Blueprints\Runtime\RuntimeInterface;

interface StepRunnerInterface {

	function setResourceMap( ResourceManager $map );

	function setRuntime( RuntimeInterface $runtime ): void;

//  @TODO: Document how this method isn't defined because
//         PHP doens't support covariant arguments
//	function run( StepInterface $input, Tracker $tracker );

}
