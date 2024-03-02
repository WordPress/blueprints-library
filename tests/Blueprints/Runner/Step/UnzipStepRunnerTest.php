<?php

namespace Blueprints\Runner\Step;

use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\Model\DataClass\UnzipStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\Resources\ResourceManager;
use WordPress\Blueprints\Runner\Step\UnzipStepRunner;
use PHPUnit\Framework\TestCase;
use WordPress\Blueprints\Runtime\Runtime;

class UnzipStepRunnerTest extends TestCase {

	/**
	 * @var string $document_root
	 */
	private string $document_root;

	/**
	 * @var Runtime $runtime
	 */
	private Runtime $runtime;

	/**
	 * @var UnzipStepRunner $step
	 */
	private UnzipStepRunner $step;

	/**
	 * @var Filesystem
	 */
	private Filesystem $file_system;

	/**
	 * @var Stub
	 */
	private Stub $resource_manager;

	/**
	 * @before
	 */
	public function before() {
		$this->document_root = Path::makeAbsolute( 'test', sys_get_temp_dir() );
		$this->runtime       = new Runtime( $this->document_root );

		$this->resource_manager = $this->createStub( ResourceManager::class );

		$this->step = new UnzipStepRunner();
		$this->step->setRuntime( $this->runtime );
		$this->step->setResourceManager( $this->resource_manager );

		$this->file_system = new Filesystem();
	}

	/**
	 * @after
	 */
	public function after() {
		$this->file_system->remove( $this->document_root );
	}

	public function testUnzipFileWhenUsingAbsolutePath(): void {
		$zip = __DIR__ . '/test_zip.zip';
		$this->resource_manager->method( 'getStream' )
			->willReturn( fopen( $zip, 'rb' ) );

		$input = new UnzipStep();
		$input->setZipFile( $zip );
		$extracted_file_path = $this->runtime->resolvePath( 'dir/test_zip.txt' );
		$input->setExtractToPath( Path::getDirectory( $extracted_file_path ) );

		$this->step->run( $input, new Tracker() );

		$this->assertFileEquals( __DIR__ . '/test_zip.txt', $extracted_file_path );
	}

	public function testUnzipFileWhenUsingRelativePath(): void {
		$zip = __DIR__ . '/test_zip.zip';
		$this->resource_manager->method( 'getStream' )
			->willReturn( fopen( $zip, 'rb' ) );

		$input = new UnzipStep();
		$input->setZipFile( $zip );
		$input->setExtractToPath( 'dir' );

		$this->step->run( $input, new Tracker() );

		$extracted_file_path = $this->runtime->resolvePath( 'dir/test_zip.txt' );
		$this->assertFileEquals( __DIR__ . '/test_zip.txt', $extracted_file_path );
	}
}
