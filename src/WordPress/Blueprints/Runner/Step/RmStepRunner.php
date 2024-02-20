<?php

namespace WordPress\Blueprints\Runner\Step;

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
        try {
            $relativePath = $this->getRuntime()->resolvePath( $input->path );
            $fileSystem = new Filesystem();
            $fileSystem->remove($relativePath);
        } catch (IOException $exception) {
            throw new BlueprintException("Failed to remove the directory or file at {$input->path}", 0, $exception);
        }
    }
}