<?php

namespace WordPress\Blueprints\Runner\Step;

use Symfony\Component\Filesystem\Exception\IOException;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\MkdirStep;


class MkdirStepRunner extends BaseStepRunner {

    function run(MkdirStep $input) {
        $path = $input->path;
        $this->fileManager->assertNoFileExists($path);
        $this->fileManager->mkdir($path);
	}
}