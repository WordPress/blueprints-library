<?php

namespace Blueprints\Runner\Step;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\CpStep;
use WordPress\Blueprints\Runner\Step\CpStepRunner;
use PHPUnit\Framework\TestCase;
use WordPress\Blueprints\Runtime\NativePHPRuntime;

class CpStepRunnerTest extends TestCase
{
    /**
     * @var string
     */
    private string $documentRoot;

    /**
     * @var NativePHPRuntime
     */
    private NativePHPRuntime $runtime;

    /**
     * @var CpStepRunner
     */
    private CpStepRunner $step;

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
        $this->runtime = new NativePHPRuntime($this->documentRoot);

        $this->step = new CpStepRunner();
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

    public function testCopiesFileWhenUsingAbsolutePath(): void
    {
        $relativeFromPath = "fromDir/file.txt";
        $absoluteFromPath = $this->runtime->resolvePath($relativeFromPath);
        $this->fileSystem->dumpFile($absoluteFromPath, "from");

        $relativeToPath = "toDir/file.txt";
        $absoluteToPath = $this->runtime->resolvePath($relativeToPath);

        $input = new CpStep();
        $input->fromPath = $absoluteFromPath;
        $input->toPath = dirname($absoluteToPath);

        $this->step->run($input);

        $this->assertFileExists($absoluteToPath);
        $this->assertFileEquals($absoluteFromPath, $absoluteToPath);
    }

    public function testCopiesFileWhenUsingRelativePath(): void
    {
        $relativeFromPath = "fromDir/file.txt";
        $absoluteFromPath = $this->runtime->resolvePath($relativeFromPath);
        $this->fileSystem->dumpFile($absoluteFromPath, "from");

        $relativeToPath = "toDir/file.txt";
        $absoluteToPath = $this->runtime->resolvePath($relativeToPath);

        $input = new CpStep();
        $input->fromPath = $relativeFromPath;
        $input->toPath = dirname($relativeToPath);

        $this->step->run($input);

        $this->assertFileExists($absoluteToPath);
        $this->assertFileEquals($absoluteFromPath, $absoluteToPath);
    }

    public function testCopiesAndOverwritesFileWhenToFileExistsAndIsOlder(): void
    {
        $relativeToPath = "toDir/file.txt";
        $absoluteToPath = $this->runtime->resolvePath($relativeToPath);
        $this->fileSystem->dumpFile($absoluteToPath, "to");

        $relativeFromPath = "fromDir/file.txt";
        $absoluteFromPath = $this->runtime->resolvePath($relativeFromPath);
        $this->fileSystem->dumpFile($absoluteFromPath, "from");

        $input = new CpStep();
        $input->fromPath = $relativeFromPath;
        $input->toPath = $relativeToPath;

        $this->step->run($input);

        $this->assertFileExists($absoluteToPath);
        $this->assertFileEquals($absoluteFromPath, $absoluteToPath);
    }

    public function testCopiesAndOverwritesFileWhenToFileExistsAndIsNewer(): void
    {
        $relativeFromPath = "fromDir/file.txt";
        $absoluteFromPath = $this->runtime->resolvePath($relativeFromPath);
        $this->fileSystem->dumpFile($absoluteFromPath, "from");

        $relativeToPath = "toDir/file.txt";
        $absoluteToPath = $this->runtime->resolvePath($relativeToPath);
        $this->fileSystem->dumpFile($absoluteToPath, "to");

        $input = new CpStep();
        $input->fromPath = $relativeFromPath;
        $input->toPath = $relativeToPath;

        $this->step->run($input);

        $this->assertFileExists($absoluteToPath);
        $this->assertFileEquals($absoluteFromPath, $absoluteToPath);
    }

    public function testCopiesFilesFromOneDirectoryToAnother(): void
    {
        $relativeFromPath1 = "fromDir/file1.txt";
        $absoluteFromPath1 = $this->runtime->resolvePath($relativeFromPath1);
        $this->fileSystem->dumpFile($absoluteFromPath1, "from1");

        $relativeFromPath2 = "fromDir/file2.txt";
        $absoluteFromPath2 = $this->runtime->resolvePath($relativeFromPath2);
        $this->fileSystem->dumpFile($absoluteFromPath2, "from2");

        $relativeToPath1 = "toDir/file1.txt";
        $absoluteToPath1 = $this->runtime->resolvePath($relativeToPath1);

        $relativeToPath2 = "toDir/file2.txt";
        $absoluteToPath2 = $this->runtime->resolvePath($relativeToPath2);

        $input = new CpStep();
        $input->fromPath = dirname($relativeFromPath1);
        $input->toPath = dirname($relativeToPath1);

        $this->step->run($input);

        $this->assertFileExists($absoluteToPath1);
        $this->assertFileEquals($absoluteFromPath1, $absoluteToPath1);

        $this->assertFileExists($absoluteToPath2);
        $this->assertFileEquals($absoluteFromPath2, $absoluteToPath2);
    }

    public function testCopiesFilesFromOneDirectoryToAnotherRecursively(): void
    {
        $relativeFromPath1 = "fromDir/file1.txt";
        $absoluteFromPath1 = $this->runtime->resolvePath($relativeFromPath1);
        $this->fileSystem->dumpFile($absoluteFromPath1, "from1");

        $relativeFromPath2 = "fromDir/subDir/file2.txt";
        $absoluteFromPath2 = $this->runtime->resolvePath($relativeFromPath2);
        $this->fileSystem->dumpFile($absoluteFromPath2, "from2");

        $relativeToPath1 = "toDir/file1.txt";
        $absoluteToPath1 = $this->runtime->resolvePath($relativeToPath1);

        $relativeToPath2 = "toDir/subDir/file2.txt";
        $absoluteToPath2 = $this->runtime->resolvePath($relativeToPath2);

        $input = new CpStep();
        $input->fromPath = dirname($relativeFromPath1);
        $input->toPath = dirname($relativeToPath1);

        $this->step->run($input);

        $this->assertFileExists($absoluteToPath1);
        $this->assertFileEquals($absoluteFromPath1, $absoluteToPath1);

        $this->assertFileExists($absoluteToPath2);
        $this->assertFileEquals($absoluteFromPath2, $absoluteToPath2);
    }

    public function testCopiesFilesToRootDirectory(): void
    {
        $relativeFromPath = "fromDir/file.txt";
        $absoluteFromPath = $this->runtime->resolvePath($relativeFromPath);
        $this->fileSystem->dumpFile($absoluteFromPath, "from");


        $relativeToPath = "file.txt";
        $absoluteToPath = $this->runtime->resolvePath($relativeToPath);

        $input = new CpStep();
        $input->fromPath = $relativeFromPath;
        $input->toPath = "";

        $this->step->run($input);

        $this->assertFileExists($absoluteToPath);
        $this->assertFileEquals($absoluteFromPath, $absoluteToPath);
    }

    public function testThrowsExceptionWhenFromPathNotFileOrDirectory(): void
    {
        $relativeFromPath = "fromDir/file.txt";
        $absoluteFromPath = $this->runtime->resolvePath($relativeFromPath);
        $this->fileSystem->dumpFile($absoluteFromPath, "from");

        $input = new CpStep();
        $input->fromPath = $relativeFromPath;
        // $input->toPath intentionally not set

        $this->expectException(BlueprintException::class);
        $this->expectExceptionMessage("Failed to copy file from \"$absoluteFromPath\" to \"$absoluteToPath\".");
        $this->step->run($input);
    }

    public function testThrowsExceptionWhenToPathNull(): void
    {
        $relativeFromPath = "fromDir/file.txt";
        $absoluteFromPath = $this->runtime->resolvePath($relativeFromPath);

        $relativeToPath = "toDir";
        $absoluteToPath = $this->runtime->resolvePath($relativeToPath);

        $input = new CpStep();
        $input->fromPath = $relativeFromPath;
        $input->toPath = $relativeToPath;

        $this->expectException(BlueprintException::class);
        $this->expectExceptionMessage("Failed to copy file from \"$absoluteFromPath\" to \"$absoluteToPath\".");
        $this->step->run($input);
    }
}
