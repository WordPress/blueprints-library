<?php

use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use WordPress\Blueprints\Runtime\NativePHPRuntime;


beforeEach(function() {
    $this->runtime = new NativePHPRuntime(sys_get_temp_dir());

    $this->step = new RmStepRunner();
    $this->step->setRuntime($this->runtime);

    $this->fileSystem = new Filesystem();
});

it('should remove a directory when an absolute path is provided', function() {
    $unresolvedDirToRemove = "dir";
    $resolvedDirToRemove = $this->runtime->resolvePath($unresolvedDirToRemove);
    $this->fileSystem->mkdir($resolvedDirToRemove);

    $input = new RmStep();
    $input->path = $resolvedDirToRemove;

    $this->step->run($input);

    expect($this->fileSystem->exists($resolvedDirToRemove))->toBeFalse();
});

it('should remove a directory when a relative path is provided', function () {
    $unresolvedDirToRemove = "dir";
    $resolvedDirToRemove = $this->runtime->resolvePath($unresolvedDirToRemove);
    $this->fileSystem->mkdir($resolvedDirToRemove);

    $input = new RmStep();
    $input->path = $unresolvedDirToRemove;

    $this->step->run($input);

    expect($this->fileSystem->exists($resolvedDirToRemove))->toBeFalse();
});

it ('should remove a directory with a subdirectory', function () {
    $unresolvedSubDir = "dir/subdir";
    $resolvedSubDir = $this->runtime->resolvePath($unresolvedSubDir);
    $this->fileSystem->mkdir($resolvedSubDir);

    $unresolvedDirToRemove = "dir";
    $resolvedDirToRemove = $this->runtime->resolvePath($unresolvedDirToRemove);

    $input = new RmStep();
    $input->path = $unresolvedDirToRemove;

    $this->step->run($input);

    expect($this->fileSystem->exists($resolvedDirToRemove))->toBeFalse();
});

it ('should remove a directory with a file', function () {
    $unresolvedFile = "dir/file.txt";
    $resolvedFile = $this->runtime->resolvePath($unresolvedFile);
    $this->fileSystem->dumpFile($resolvedFile, "test");

    $unresolvedDirToRemove = "dir";
    $resolvedDirToRemove = $this->runtime->resolvePath($unresolvedDirToRemove);

    $input = new RmStep();
    $input->path = $unresolvedDirToRemove;

    $this->step->run($input);

    expect($this->fileSystem->exists($resolvedDirToRemove))->toBeFalse();
});

it ('should remove a file', function () {
   $unresolvedFileToRemove = "file.txt";
   $resolvedFileToRemove = $this->runtime->resolvePath($unresolvedFileToRemove);
   $this->fileSystem->dumpFile($resolvedFileToRemove, "test");

   $input = new RmStep();
   $input->path = $unresolvedFileToRemove;

   $this->step->run($input);

   expect($this->fileSystem->exists($resolvedFileToRemove))->toBeFalse();
});

it ('should throw an exception when asked with a relative path to remove a nonexistent directory ', function() {
    $dirToRemove = "dir";

    $input = new RmStep();
    $input->path = $dirToRemove;

    $resolvedDirToRemove = $this->runtime->resolvePath($dirToRemove);
    expect(fn() => $this->step->run($input))->toThrow(BlueprintException::class, "Failed to remove $resolvedDirToRemove: the directory or file does not exist.");
});

it ('should throw an exception when asked with an absolute path to remove a nonexistent directory', function () {
    $dirToRemove = "/dir";

    $input = new RmStep();
    $input->path = $dirToRemove;

    expect(fn() => $this->step->run($input))->toThrow(BlueprintException::class, "Failed to remove $dirToRemove: the directory or file does not exist.");
});

it ('should throw an exception when asked to remove a nonexistent file', function() {
    $fileToRemove = "file.txt";

    $input = new RmStep();
    $input->path = $fileToRemove;

    $resolvedFileToRemove = $this->runtime->resolvePath($fileToRemove);
    expect(fn() => $this->step->run($input))->toThrow(BlueprintException::class, "Failed to remove $resolvedFileToRemove: the directory or file does not exist.");
});
