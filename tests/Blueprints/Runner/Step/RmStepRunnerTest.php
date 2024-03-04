<?php

namespace Blueprints\Runner\Step;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use PHPUnit\Framework\TestCase;
use WordPress\Blueprints\Runtime\Runtime;

class RmStepRunnerTest extends TestCase
{
    /**
     * @var string
     */
    private string $documentRoot;

    /**
     * @var Runtime
     */
    private Runtime $runtime;

    /**
     * @var RmStepRunner
     */
    private RmStepRunner $step;

    /**
     * @var Filesystem
     */
    private Filesystem $fileSystem;

    /**
     * @before
     */
    public function before()
    {
        $this->documentRoot = Path::makeAbsolute("test", sys_get_temp_dir());
        $this->runtime = new Runtime($this->documentRoot);

        $this->step = new RmStepRunner();
        $this->step->setRuntime($this->runtime);

        $this->fileSystem = new Filesystem();
    }

    /**
     * @after
     */
    public function after()
    {
        $this->fileSystem->remove($this->documentRoot);
    }

    public function testRemoveDirectoryWhenUsingAbsolutePath(): void
    {
        $absolutePath = $this->runtime->resolvePath("dir");
        $this->fileSystem->mkdir($absolutePath);

        $input = new RmStep();
        $input->path = $absolutePath;

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist($absolutePath);
    }

    public function testRemoveDirectoryWhenUsingRelativePath(): void
    {
        $relativePath = "dir";
        $absolutePath = $this->runtime->resolvePath($relativePath);
        $this->fileSystem->mkdir($absolutePath);

        $input = new RmStep();
        $input->path = $relativePath;

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist($absolutePath);
    }

    public function testRemoveDirectoryWithSubdirectory(): void
    {
        $relativePath = "dir/subdir";
        $absolutePath = $this->runtime->resolvePath($relativePath);
        $this->fileSystem->mkdir($absolutePath);

        $input = new RmStep();
        $input->path = dirname($relativePath);

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist($absolutePath);
    }

    public function testRemoveDirectoryWithFile(): void
    {
        $relativePath = "dir/file.txt";
        $absolutePath = $this->runtime->resolvePath($relativePath);
        $this->fileSystem->dumpFile($absolutePath, "test");

        $input = new RmStep();
        $input->path = dirname($relativePath);

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist(dirname($absolutePath));
    }

    public function testRemoveFile(): void
    {
        $relativePath = "file.txt";
        $absolutePath = $this->runtime->resolvePath($relativePath);
        $this->fileSystem->dumpFile($absolutePath, "test");

        $input = new RmStep();
        $input->path = $relativePath;

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist($absolutePath);
    }

    public function testThrowExceptionWhenRemovingNonexistentDirectoryAndUsingRelativePath(): void
    {
        $relativePath = "dir";
        $absolutePath = $this->runtime->resolvePath($relativePath);

        $input = new RmStep();
        $input->path = $relativePath;

        $this->expectException(BlueprintException::class);
        $this->expectExceptionMessage("Failed to remove \"$absolutePath\": the directory or file does not exist.");
        $this->step->run($input);
    }

    public function testThrowExceptionWhenRemovingNonexistentDirectoryAndUsingAbsolutePath(): void
    {
        $absolutePath = "/dir";

        $input = new RmStep();
        $input->path = $absolutePath;

        $this->expectException(BlueprintException::class);
        $this->expectExceptionMessage("Failed to remove \"$absolutePath\": the directory or file does not exist.");
        $this->step->run($input);
    }

    public function testThrowExceptionWhenRemovingNonexistentFileAndUsingAbsolutePath(): void
    {
        $relativePath = "/file.txt";

        $input = new RmStep();
        $input->path = $relativePath;

        $this->expectException(BlueprintException::class);
        $this->expectExceptionMessage("Failed to remove \"$relativePath\": the directory or file does not exist.");
        $this->step->run($input);
    }

    public function testThrowExceptionWhenRemovingNonexistentFileAndUsingRelativePath(): void
    {
        $relativePath = "file.txt";
        $absolutePath = $this->runtime->resolvePath($relativePath);

        $input = new RmStep();
        $input->path = $relativePath;

        $this->expectException(BlueprintException::class);
        $this->expectExceptionMessage("Failed to remove \"$absolutePath\": the directory or file does not exist.");
        $this->step->run($input);
    }
}
