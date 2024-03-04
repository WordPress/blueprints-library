<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Resources\ResourceManager;
use WordPress\Blueprints\Runtime\RuntimeInterface;

interface StepRunnerInterface
{

    public function setResourceManager(ResourceManager $map);

    public function setRuntime(RuntimeInterface $runtime): void;

//  @TODO: Document how this method isn't defined because
//         PHP doens't support covariant arguments
//  function run( StepInterface $input, Tracker $tracker );
}
