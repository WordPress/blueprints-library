<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Resources\ResourceManager;
use WordPress\Blueprints\Runtime\RuntimeInterface;

abstract class BaseStepRunner implements StepRunnerInterface
{
    protected ResourceManager $resourceManager;

    protected RuntimeInterface $runtime;

    public function setResourceManager(ResourceManager $map)
    {
        $this->resourceManager = $map;
    }

    protected function getResource($declaration)
    {
        return $this->resourceManager->getStream($declaration);
    }

    public function setRuntime(RuntimeInterface $runtime): void
    {
        $this->runtime = $runtime;
    }

    protected function getRuntime(): RuntimeInterface
    {
        return $this->runtime;
    }

    protected function getDefaultCaption($input): ?string
    {
        return null;
    }
}
