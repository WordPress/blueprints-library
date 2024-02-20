<?php

use WordPress\Blueprints\Model\Builder\RmDirStepBuilder;
use WordPress\Blueprints\Runner\Step\RmDirStepRunner;

it('should remove a directory', function () {
    $path = __DIR__ . "/dir";
    mkdir($path);
    expect( file_exists($path)) -> toBeTrue();

    $stepBuilder = new RmDirStepBuilder();
    $stepBuilder->setPath($path);
    $input = $stepBuilder->toDataObject();
    $step = new RmDirStepRunner();
    $step->run($input);

    expect( file_exists($path)) -> toBeFalse();
});

it ('should remove a directory with a subdirectory', function () {
    $path = __DIR__ . "/dir";
    $subpath = __DIR__ . "/dir/subdir";
    mkdir($subpath, 0777, true);
    expect(file_exists($path))->toBeTrue()
        ->and(file_exists($subpath))->toBeTrue();

    $stepBuilder = new RmDirStepBuilder();
    $input = $stepBuilder
        ->setPath($path)
        ->toDataObject();
    $step = new RmDirStepRunner();

    $step->run($input);

    expect(file_exists($path))->toBeFalse()
        ->and(file_exists($subpath))->toBeFalse();
});

it ('should do nothing when asked to remove a nonexistent directory', function() {
    $path = __DIR__ . "/dir";

    $stepBuilder = new RmDirStepBuilder();
    $input = $stepBuilder
        ->setPath($path)
        ->toDataObject();
    $step = new RmDirStepRunner();

    $step->run($input);
})->throwsNoExceptions();
