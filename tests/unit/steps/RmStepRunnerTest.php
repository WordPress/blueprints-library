<?php

namespace unit\steps;

use PHPUnitTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use WordPress\Blueprints\Runtime\Runtime;

class RmStepRunnerTest extends PHPUnitTestCase
{
    /**
     * @var string
     */
    private $documentRoot;

    /**
     * @var Runtime
     */
    private $runtime;

    /**
     * @var RmStepRunner
     */
    private $step;

    /**
     * @var Filesystem
     */
    private $fileSystem;

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

    public function testRemoveDirectoryWhenUsingAbsolutePath()
    {
        $absolutePath = $this->runtime->resolvePath("dir");
        $this->fileSystem->mkdir($absolutePath);

        $input = new RmStep();
        $input->path = $absolutePath;

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist($absolutePath);
    }

    public function testRemoveDirectoryWhenUsingRelativePath()
    {
        $relativePath = "dir";
        $absolutePath = $this->runtime->resolvePath($relativePath);
        $this->fileSystem->mkdir($absolutePath);

        $input = new RmStep();
        $input->path = $relativePath;

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist($absolutePath);
    }

    public function testRemoveDirectoryWithSubdirectory()
    {
        $relativePath = "dir/subdir";
        $absolutePath = $this->runtime->resolvePath($relativePath);
        $this->fileSystem->mkdir($absolutePath);

        $input = new RmStep();
        $input->path = dirname($relativePath);

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist($absolutePath);
    }

    public function testRemoveDirectoryWithFile()
    {
        $relativePath = "dir/file.txt";
        $absolutePath = $this->runtime->resolvePath($relativePath);
        $this->fileSystem->dumpFile($absolutePath, "test");

        $input = new RmStep();
        $input->path = dirname($relativePath);

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist(dirname($absolutePath));
    }

    public function testRemoveFile()
    {
        $relativePath = "file.txt";
        $absolutePath = $this->runtime->resolvePath($relativePath);
        $this->fileSystem->dumpFile($absolutePath, "test");

        $input = new RmStep();
        $input->path = $relativePath;

        $this->step->run($input);

        $this->assertDirectoryDoesNotExist($absolutePath);
    }

    public function testThrowExceptionWhenRemovingNonexistentDirectoryAndUsingRelativePath()
    {
        $relativePath = "dir";
        $absolutePath = $this->runtime->resolvePath($relativePath);

        $input = new RmStep();
        $input->path = $relativePath;

        $this->expectException(BlueprintException::class);
        $this->expectExceptionMessage("Failed to remove \"$absolutePath\": the directory or file does not exist.");
        $this->step->run($input);
    }

    public function testThrowExceptionWhenRemovingNonexistentDirectoryAndUsingAbsolutePath()
    {
        $absolutePath = "/dir";

        $input = new RmStep();
        $input->path = $absolutePath;

        $this->expectException(BlueprintException::class);
        $this->expectExceptionMessage("Failed to remove \"$absolutePath\": the directory or file does not exist.");
        $this->step->run($input);
    }

    public function testThrowExceptionWhenRemovingNonexistentFileAndUsingAbsolutePath()
    {
        $relativePath = "/file.txt";

        $input = new RmStep();
        $input->path = $relativePath;

        $this->expectException(BlueprintException::class);
        $this->expectExceptionMessage("Failed to remove \"$relativePath\": the directory or file does not exist.");
        $this->step->run($input);
    }

    public function testThrowExceptionWhenRemovingNonexistentFileAndUsingRelativePath()
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
