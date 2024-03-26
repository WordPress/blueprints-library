<?php

namespace unit\steps;

use PHPUnitTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\Model\DataClass\MkdirStep;
use WordPress\Blueprints\Runner\Step\MkdirStepRunner;
use WordPress\Blueprints\Runtime\Runtime;
use WordPress\Blueprints\BlueprintException;

class MkdirStepRunnerTest extends PHPUnitTestCase {
	/**
	 * @var string
	 */
	private $document_root;

	/**
	 * @var Runtime
	 */
	private $runtime;

	/**
	 * @var MkdirStepRunner
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

		$this->step_runner = new MkdirStepRunner();
		$this->step_runner->setRuntime( $this->runtime );

		$this->filesystem = new Filesystem();
	}

	/**
	 * @after
	 */
	public function after() {
		$this->filesystem->remove( $this->document_root );
	}

    public function testCreateDirectoryWhenUsingRelativePath() {
        $path = 'dir';
        $step = new MkdirStep();
		$step->setPath( $path );

        $this->step_runner->run( $step );
		$resolved_path = $this->runtime->resolvePath( $path );

		self::assertDirectoryExists( $resolved_path );
    }

    public function testCreateDirectoryWhenUsingAbsoluteAbsolutePath() {
        $relative_path = 'dir';
        $resolved_path = $this->runtime->resolvePath( $relative_path );

        $step = new MkdirStep();
        $step->setPath( $resolved_path );

        $this->step_runner->run( $step );
        
        self::assertDirectoryExists( $resolved_path );
    }

    public function testCreateDirectoryRecursively() {
        $path = 'dir/subdir';
        $step = new MkdirStep();
        $step->setPath( $path );

        $this->step_runner->run( $step );

        $resolved_path = $this->runtime->resolvePath( $path );
        self::assertDirectoryExists( $resolved_path );
    }

	public function testCreateDirectoryWithProperMode() {
		$path = 'dir';
		$step = new MkdirStep();
		$step->setPath( $path );

		$this->step_runner->run( $step );

		$resolved_path = $this->runtime->resolvePath( $path );
		self::assertDirectoryExists( $resolved_path );
		self::assertDirectoryIsWritable( $resolved_path );
		self::assertDirectoryIsReadable( $resolved_path );
	}
    
    public function testThrowExceptionWhenCreatingDirectoryWhenDirectoryAlreadyExists() {
        $path = 'dir';
        $resolved_path = $this->runtime->resolvePath( $path );

        $step = new MkdirStep();
        $step->setPath( $path );

		$this->step_runner->run( $step );

		self::expectException( BlueprintException::class );
		self::expectExceptionMessage( "Failed to create \"$resolved_path\": the directory exists." );
        $this->step_runner->run( $step );
    }
}
