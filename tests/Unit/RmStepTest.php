<?php

use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use WordPress\Blueprints\Runtime\NativePHPRuntime;


beforeEach(function() {
    $this->step = new RmStepRunner();
    $documentRoot = sys_get_temp_dir();
    $this->runtime = new NativePHPRuntime($documentRoot);
    $this->step->setRuntime($this->runtime);
});

it('should remove a directory when an absolute path provided', function() {
    $unresolvedDirToRemove = "dir";
    $resolvedDirToRemove = $this->runtime->resolvePath($unresolvedDirToRemove);
    mkdir($resolvedDirToRemove, 0777, true);
    expect(file_exists($resolvedDirToRemove))->toBeTrue();

    $input = new RmStep();
    $input->path = $resolvedDirToRemove;

    $this->step->run($input);

    expect(file_exists($resolvedDirToRemove))->toBeFalse();
});

it('should remove a directory when a relative path provided', function () {
    $unresolvedDirToRemove = "dir";
    $resolvedDirToRemove = $this->runtime->resolvePath($unresolvedDirToRemove);
    mkdir($resolvedDirToRemove, 0777, true);
    expect(file_exists($resolvedDirToRemove))->toBeTrue();

    $input = new RmStep();
    $input->path = $unresolvedDirToRemove;

    $this->step->run($input);

    expect(file_exists($resolvedDirToRemove))->toBeFalse();
});

it ('should remove a directory with a subdirectory', function () {
    $unresolvedSubDir = "dir/subdir";
    $resolvedSubDir = $this->runtime->resolvePath($unresolvedSubDir);
    mkdir($resolvedSubDir, 0777, true);
    expect(file_exists($resolvedSubDir))->toBeTrue();

    $unresolvedDirToRemove = "dir";
    $resolvedDirToRemove = $this->runtime->resolvePath($unresolvedDirToRemove);

    $input = new RmStep();
    $input->path = $unresolvedDirToRemove;

    $this->step->run($input);

    expect(file_exists($resolvedDirToRemove))->toBeFalse();
});

it ('should remove a directory with a file', function () {
    $unresolvedDirToRemove = "dir";
    $resolvedDirToRemove = $this->runtime->resolvePath($unresolvedDirToRemove);
    mkdir($resolvedDirToRemove, 0777, true);
    expect(file_exists($resolvedDirToRemove))->toBeTrue();

    $unresolvedFile = "dir/file.txt";
    $resolvedFile = $this->runtime->resolvePath($unresolvedFile);
    file_put_contents($resolvedFile, "test");

    $input = new RmStep();
    $input->path = $unresolvedDirToRemove;

    $this->step->run($input);

    expect(file_exists($resolvedDirToRemove))->toBeFalse();
});

it ('should remove a file', function () {
   $unresolvedFileToRemove = "file.txt";
   $resolvedFileToRemove = $this->runtime->resolvePath($unresolvedFileToRemove);
   file_put_contents($resolvedFileToRemove, "test");
   expect(file_exists($resolvedFileToRemove))->toBeTrue();

   $input = new RmStep();
   $input->path = $unresolvedFileToRemove;

   $this->step->run($input);

   expect(file_exists($resolvedFileToRemove))->toBeFalse();
});

it ('should throw an exception when asked with a relative path to remove a nonexistent directory ', function() {
    $dirToRemove = "dir";

    $input = new RmStep();
    $input->path = $dirToRemove;

    $this->step->run($input);
})->throws(BlueprintException::class, "Failed to remove the directory or file.");

it ('should throw an exception when asked with an absolute path to remove a nonexistent directory', function () {
    $dirToRemove = "/dir";

    $input = new RmStep();
    $input->path = $dirToRemove;

    $this->step->run($input);
})->throws(BlueprintException::class, "Failed to remove the directory or file.");

it ('should throw an exception when asked to remove a nonexistent file', function() {
    $fileToRemove = "file.txt";

    $input = new RmStep();
    $input->path = $fileToRemove;

    $this->step->run($input);
})->throws(BlueprintException::class, "Failed to remove the directory or file.");
