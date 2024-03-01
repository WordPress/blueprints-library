<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use WordPress\Blueprints\Runtime\Runtime;


beforeEach( function () {
	$this->documentRoot = Path::makeAbsolute( "test", sys_get_temp_dir() );
	$this->runtime = new Runtime( $this->documentRoot );

	$this->step = new RmStepRunner();
	$this->step->setRuntime( $this->runtime );

	$this->fileSystem = new Filesystem();
} );

afterEach( function () {
	$this->fileSystem->remove( $this->documentRoot );
} );

it( 'should remove a directory (using an absolute path)', function () {
	$absolutePath = $this->runtime->resolvePath( "dir" );
	$this->fileSystem->mkdir( $absolutePath );

	$input = new RmStep();
	$input->path = $absolutePath;

	$this->step->run( $input );

	expect( $this->fileSystem->exists( $absolutePath ) )->toBeFalse();
} );

it( 'should remove a directory (using a relative path)', function () {
	$relativePath = "dir";
	$absolutePath = $this->runtime->resolvePath( $relativePath );
	$this->fileSystem->mkdir( $absolutePath );

	$input = new RmStep();
	$input->path = $relativePath;

	$this->step->run( $input );

	expect( $this->fileSystem->exists( $absolutePath ) )->toBeFalse();
} );

it( 'should remove a directory with a subdirectory', function () {
	$relativePath = "dir/subdir";
	$absolutePath = $this->runtime->resolvePath( $relativePath );
	$this->fileSystem->mkdir( $absolutePath );

	$input = new RmStep();
	$input->path = dirname( $relativePath );

	$this->step->run( $input );

	expect( $this->fileSystem->exists( dirname( $absolutePath ) ) )->toBeFalse();
} );

it( 'should remove a directory with a file', function () {
	$relativePath = "dir/file.txt";
	$absolutePath = $this->runtime->resolvePath( $relativePath );
	$this->fileSystem->dumpFile( $absolutePath, "test" );

	$input = new RmStep();
	$input->path = dirname( $relativePath );

	$this->step->run( $input );

	expect( $this->fileSystem->exists( dirname( $absolutePath ) ) )->toBeFalse();
} );

it( 'should remove a file', function () {
	$relativePath = "file.txt";
	$absolutePath = $this->runtime->resolvePath( $relativePath );
	$this->fileSystem->dumpFile( $absolutePath, "test" );

	$input = new RmStep();
	$input->path = $relativePath;

	$this->step->run( $input );

	expect( $this->fileSystem->exists( $absolutePath ) )->toBeFalse();
} );

it( 'should throw an exception when asked to remove a nonexistent directory (using a relative path)', function () {
	$relativePath = "dir";

	$input = new RmStep();
	$input->path = $relativePath;

	$absolutePath = $this->runtime->resolvePath( $relativePath );
	expect( fn() => $this->step->run( $input ) )->toThrow( BlueprintException::class,
		"Failed to remove \"$absolutePath\": the directory or file does not exist." );
} );

it( 'should throw an exception when asked to remove a nonexistent directory (using an absolute path)', function () {
	$absolutePath = "/dir";

	$input = new RmStep();
	$input->path = $absolutePath;

	expect( fn() => $this->step->run( $input ) )->toThrow( BlueprintException::class,
		"Failed to remove \"$absolutePath\": the directory or file does not exist." );
} );

it( 'should throw an exception when asked to remove a nonexistent file', function () {
	$relativePath = "file.txt";

	$input = new RmStep();
	$input->path = $relativePath;

	$absolutePath = $this->runtime->resolvePath( $relativePath );
	expect( fn() => $this->step->run( $input ) )->toThrow( BlueprintException::class,
		"Failed to remove \"$absolutePath\": the directory or file does not exist." );
} );
