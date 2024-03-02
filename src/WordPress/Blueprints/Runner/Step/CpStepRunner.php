<?php

namespace WordPress\Blueprints\Runner\Step;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\CpStep;

class CpStepRunner extends BaseStepRunner
{
    /**
     * @param CpStep $input
     */
    public function run(CpStep $input)
    {
        $resolvedFromPath = $this->getRuntime()->resolvePath($input->fromPath);
        $resolvedToPath = $this->getRuntime()->resolvePath($input->toPath);

        $fileSystem = new Filesystem();

        try {
            // TODO affirm it should overwrite newer files
            // Filesystem's copy requires flag `overwriteNewerFiles` set to `true`

            // TODO affirm it should copy file to directory
            // copy throws an exception when $resolvedToPath is a directory - linux would handle this:
            // When the program has one or more arguments of path names of files and following those an argument
            // of a path to a directory, then the program copies each source file to the destination directory,
            // creating any files not already existing.

            // TODO affirm it should copy all files from one directory to another (also, ponder recursivity)
            // copy throws an exception when $resolvedToPath or $resolvedFromPath are directories - linux would
            // handle this:
            // When the program's arguments are the path names to two directories,
            // cp copies all files in the source directory to the destination directory,
            // creating any files or directories needed. This mode of operation requires an additional option flag,
            // typically r, to indicate the recursive copying of directories.
            // If the destination directory already exists, the source is copied into the destination,
            // while a new directory is created if the destination does not exist.

            // TODO establish how $input->toPath should be handled
            // should copy to document root when null or when ""?

            $fileSystem->copy($resolvedFromPath, $resolvedToPath, true);
        } catch (IOException $exception) {
            throw new BlueprintException(
                "Failed to copy file from \"$resolvedFromPath\" to \"$resolvedToPath\".",
                0,
                $exception
            );
        }
    }
}
