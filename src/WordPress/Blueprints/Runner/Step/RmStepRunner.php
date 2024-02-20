<?php

namespace WordPress\Blueprints\Runner\Step;

use Mockery\Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmStep;


class RmStepRunner extends BaseStepRunner
{
    /**
     * @param RmStep $input
     */
    function run(RmStep $input) {
        $resolvedPath = $this->getRuntime()->resolvePath($input->path);
        $fileSystem = new Filesystem();
        if (false === $fileSystem->exists($resolvedPath)) {
            throw new BlueprintException("Failed to remove the directory or file. No directory or file found at $resolvedPath", 0);
        }
        try {
            $fileSystem->remove($resolvedPath);
        } catch (IOException $exception) {
            throw new BlueprintException("Failed to remove the directory or file at $resolvedPath", 0);
        }
    }
}