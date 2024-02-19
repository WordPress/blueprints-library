<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\UnzipWordPressStep;
use WordPress\Blueprints\Progress\Tracker;

class UnzipWordPressStepRunner extends InstallAssetStepRunner {

	public function run(
		UnzipWordPressStep $input,
		Tracker $progress = null
	) {
		$progress?->set( 10, 'Extracting WordPress...' );

		$this->unzipAssetTo( $input->zipFile, $this->getRuntime()->resolvePath( $input->extractToPath ) );
	}

}
