<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\MkdirStep;
use WordPress\Blueprints\Runner\Step\MkdirStepRunner;
use WordPress\Blueprints\Runtime\NativePHPRuntime;

beforeEach(function() {
    $this->documentRoot = Path::makeAbsolute("test", sys_get_temp_dir());

    $this->fileSystem = new Filesystem();
    $this->fileSystem->mkdir($this->documentRoot);

    $this->runtime = new NativePHPRuntime($this->documentRoot);

    $this->step = new MkdirStepRunner();
    $this->step->setRuntime($this->runtime);
});

afterEach(function() {
    $this->fileSystem->remove($this->documentRoot);
});

it('should make a directory when an absolute path is provided', function() {
    $unresolvedDirToMake = "dir";
    $resolvedDirToMake = $this->runtime->resolvePath($unresolvedDirToMake);

    $input = new MkdirStep();
    $input->path = $resolvedDirToMake;

    $this->step->run($input);

    expect($this->fileSystem->exists($resolvedDirToMake))->toBeTrue();
});

it('should make a directory when a relative path is provided', function () {
    $unresolvedDirToMake = "dir";
    $resolvedDirToMake = $this->runtime->resolvePath($unresolvedDirToMake);

    $input = new MkdirStep();
    $input->path = $unresolvedDirToMake;

    $this->step->run($input);

    expect($this->fileSystem->exists($resolvedDirToMake))->toBeTrue();
});

it('should make a directory recursively', function () {
    $unresolvedDirToMake = "absentParentDir/dir";
    $resolvedDirToMake = $this->runtime->resolvePath($unresolvedDirToMake);

    $input = new MkdirStep();
    $input->path = $unresolvedDirToMake;

    $this->step->run($input);

    expect($this->fileSystem->exists($resolvedDirToMake))->toBeTrue();
});

it('should throw an exception when asked to make a directory that already exists', function() {
    $unresolvedDirToMake = "dir";
    $resolvedDirToMake = $this->runtime->resolvePath($unresolvedDirToMake);

    $this->fileSystem->mkdir($resolvedDirToMake);

    $input = new MkdirStep();
    $input->path = $unresolvedDirToMake;

    expect(fn() => $this->step->run($input))
        ->toThrow(BlueprintException::class, "Failed to make $resolvedDirToMake: the directory exists already.");
});

it('should throw an exception when asked to make a directory where a file with that name already exists', function() {
    $unresolvedDirToMake = "dir";
    $resolvedDirToMake = $this->runtime->resolvePath($unresolvedDirToMake);

    $this->fileSystem->dumpFile($resolvedDirToMake, "test");

    $input = new MkdirStep();
    $input->path = $unresolvedDirToMake;

    expect(fn() => $this->step->run($input))
        ->toThrow(BlueprintException::class, "Failed to make $resolvedDirToMake: a file with that name exists already.");
});
