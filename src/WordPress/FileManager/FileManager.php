<?php

namespace WordPress\FileManager;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Runtime\RuntimeInterface;

class FileManager {

    private RuntimeInterface $runtime;

    private Filesystem $filesystem;

    function __construct(RuntimeInterface $runtime) {
        $this->filesystem = new Filesystem();
        $this->runtime = $runtime;
    }

    public function toAbsolutePath(string $path): string {
        return Path::makeAbsolute($path, $this->runtime->getDocumentRoot());
    }

    public function mkdir(string $path): void {
        $absolutePath = $this->toAbsolutePath($path);
        try {
            $this->filesystem->mkdir($absolutePath);
        } catch (IOException $exception) {
            throw new BlueprintException("Failed to create directory at $absolutePath.", 0, $exception);
        }
    }

    public function assertFileExists($path) {
        $absolutePath = $this->toAbsolutePath($path);
        if (!file_exists($absolutePath)) {
            throw new BlueprintException("Fail. No file at \"$absolutePath\" exists.");
        }
    }

    public function rename(string $fromPath, string $toPath): void {
        $absoluteFromPath = $this->toAbsolutePath($fromPath);
        $absoluteToPath = $this->toAbsolutePath($toPath);
        $isSuccessful = rename($absoluteFromPath, $absoluteToPath);
        if (!$isSuccessful) {
            throw new BlueprintException( "Failed to move the file from \"$absoluteFromPath\" at \"$absoluteToPath\"." );
        }
    }

    public function assertNoFileExists($path): void {
        $absolutePath = $this->toAbsolutePath($path);
        if (!file_exists($absolutePath)) {
            return;
        }
        if (is_dir($absolutePath)) {
            throw new BlueprintException("Fail. A directory at \"$absolutePath\" exists.");
        }
        if (is_file($absolutePath)) {
            throw new BlueprintException("Fail. A file at \"$absolutePath\" exists.");
        }
    }


}