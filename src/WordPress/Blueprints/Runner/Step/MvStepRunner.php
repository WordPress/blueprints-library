<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\MvStep;


class MvStepRunner extends BaseStepRunner {
	/**
	 * @param MvStep $input
	 */
	function run( MvStep $input ) {
        $fromPath = $input->fromPath;
        $toPath = $input->toPath;
        $this->fileManager->assertFileExists($fromPath);
        $this->fileManager->assertNoFileExists($toPath);
        $this->fileManager->rename($fromPath, $toPath);
	}
}
