<?php

namespace unit\steps;

use PHPUnitTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use WordPress\Blueprints\Runtime\Runtime;

class RmStepRunnerTest extends PHPUnitTestCase {
    /**
     * @var string
     */
    private $document_root;

    /**
     * @var Runtime
     */
    private $runtime;

    /**
     * @var RmStepRunner
     */
    private $step_runner;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @before
     */
    public function before() {
        $this->document_root = Path::makeAbsolute( "test", sys_get_temp_dir() );
        $this->runtime = new Runtime( $this->document_root );

        $this->step_runner = new RmStepRunner();
        $this->step_runner->setRuntime( $this->runtime );

        $this->filesystem = new Filesystem();
    }

    /**
     * @after
     */
    public function after() {
        $this->filesystem->remove( $this->document_root );
    }

    public function testRemoveDirectoryWhenUsingAbsolutePath() {
        $absolute_path = $this->runtime->resolvePath( "dir" );
        $this->filesystem->mkdir( $absolute_path );

        $step = new RmStep();
        $step->path = $absolute_path;

        $this->step_runner->run( $step );

		self::assertDirectoryDoesNotExist( $absolute_path );
    }

    public function testRemoveDirectoryWhenUsingRelativePath() {
        $relative_path = "dir";
        $absolute_path = $this->runtime->resolvePath( $relative_path );
        $this->filesystem->mkdir( $absolute_path );

        $step = new RmStep();
        $step->path = $relative_path;

        $this->step_runner->run( $step );

        self::assertDirectoryDoesNotExist( $absolute_path );
    }

    public function testRemoveDirectoryWithSubdirectory() {
        $relative_path = "dir/subdir";
        $absolute_path = $this->runtime->resolvePath( $relative_path );
        $this->filesystem->mkdir( $absolute_path );

        $step = new RmStep();
        $step->path = dirname( $relative_path );

        $this->step_runner->run( $step );

        self::assertDirectoryDoesNotExist( $absolute_path );
    }

    public function testRemoveDirectoryWithFile() {
        $relative_path = "dir/file.txt";
        $absolute_pPath = $this->runtime->resolvePath( $relative_path );
        $this->filesystem->dumpFile( $absolute_pPath, "test" );

        $step = new RmStep();
        $step->path = dirname( $relative_path );

        $this->step_runner->run( $step );

        self::assertDirectoryDoesNotExist( dirname( $absolute_pPath ) );
    }

    public function testRemoveFile() {
        $relative_path = "file.txt";
        $absolute_path = $this->runtime->resolvePath( $relative_path );
        $this->filesystem->dumpFile( $absolute_path, "test" );

        $step = new RmStep();
        $step->path = $relative_path;

        $this->step_runner->run( $step );

        self::assertDirectoryDoesNotExist( $absolute_path );
    }

    public function testThrowExceptionWhenRemovingNonexistentDirectoryAndUsingRelativePath() {
        $relative_path = "dir";
        $absolute_path = $this->runtime->resolvePath( $relative_path );

        $step = new RmStep();
        $step->path = $relative_path;

        self::expectException( BlueprintException::class );
        self::expectExceptionMessage( "Failed to remove \"$absolute_path\": the directory or file does not exist." );
        $this->step_runner->run( $step );
    }

    public function testThrowExceptionWhenRemovingNonexistentDirectoryAndUsingAbsolutePath() {
        $absolute_path = "/dir";

        $step = new RmStep();
        $step->path = $absolute_path;

		self::expectException( BlueprintException::class );
		self::expectExceptionMessage( "Failed to remove \"$absolute_path\": the directory or file does not exist." );
        $this->step_runner->run( $step );
    }

    public function testThrowExceptionWhenRemovingNonexistentFileAndUsingAbsolutePath() {
        $relative_path = "/file.txt";

        $step = new RmStep();
        $step->path = $relative_path;

		self::expectException( BlueprintException::class );
		self::expectExceptionMessage( "Failed to remove \"$relative_path\": the directory or file does not exist." );
        $this->step_runner->run( $step );
    }

    public function testThrowExceptionWhenRemovingNonexistentFileAndUsingRelativePath() {
        $relativePath = "file.txt";
        $absolutePath = $this->runtime->resolvePath($relativePath);

        $step = new RmStep();
        $step->path = $relativePath;

		self::expectException( BlueprintException::class );
		self::expectExceptionMessage( "Failed to remove \"$absolutePath\": the directory or file does not exist." );
        $this->step_runner->run( $step );
    }
}
