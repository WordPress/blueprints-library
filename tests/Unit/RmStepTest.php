<?php

use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use WordPress\Blueprints\Runtime\NativePHPRuntime;


beforeEach(function() {
    $this->step = new RmStepRunner();
    $this->step->setRuntime(new NativePHPRuntime("root"));
});


it('should remove a directory', function () {
    $dirToRemove = "/root/dir";
    mkdir($dirToRemove, 0777, true);
    expect(file_exists($dirToRemove))->toBeTrue();

    $input = new RmStep();
    $input->path = $dirToRemove;

    $this->step->run($input);

    expect(file_exists($dirToRemove))->toBeFalse();
});

it ('should remove a directory with a subdirectory', function () {
    $dirToRemove = "/root/dir";
    $subDir = "/root/dir/subdir";
    mkdir($subDir, 0777, true);
    expect(file_exists($subDir))->toBeTrue();

    $input = new RmStep();
    $input->path = $dirToRemove;

    $this->step->run($input);

    expect(file_exists($dirToRemove))->toBeFalse();
});

it ('should remove a directory with a file', function () {
    $dirToRemove = "/root/dir";
    mkdir($dirToRemove, 0777, true);
    file_put_contents("root/dir/file.txt", "test");
    expect(file_exists($dirToRemove))->toBeTrue();

    $input = new RmStep();
    $input->path = $dirToRemove;

    $this->step->run($input);

    expect(file_exists($dirToRemove))->toBeFalse();
});

it ('should remove a file', function () {
   $fileToRemove = "/root/file.txt";
   file_put_contents($fileToRemove, "test");
   expect(file_exists($fileToRemove))->toBeTrue();

   $input = new RmStep();
   $input->path = $fileToRemove;

   $this->step->run($input);

   expect(file_exists($fileToRemove))->toBeFalse();
});

it ('should do nothing when asked to remove a nonexistent directory', function() {
    $dirToRemove = "/root/dir";

    $input = new RmStep();
    $input->path = $dirToRemove;

    $this->step->run($input);
})->throwsNoExceptions();

it ('should do nothing when asked to remove a nonexistent file', function() {
    $fileToRemove = "/root/file.txt";

    $input = new RmStep();
    $input->path = $fileToRemove;

    $this->step->run($input);
})->throwsNoExceptions();