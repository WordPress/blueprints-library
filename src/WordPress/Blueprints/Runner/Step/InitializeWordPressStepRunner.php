<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\InitializeWordPressStep;
use WordPress\Blueprints\Progress\Tracker;

class InitializeWordPressStepRunner extends InstallAssetStepRunner {

	public function run(
		InitializeWordPressStep $input,
		Tracker $progress = null
	) {
		$progress?->set( 10, 'Extracting WordPress...' );

		$this->unzipAssetTo( $input->wordPressZip, $this->getRuntime()->getDocumentRoot() );

		$cofigSample = $this->getRuntime()->resolvePath( 'wp-config-sample.php' );
		$cofig = $this->getRuntime()->resolvePath( 'wp-config.php' );
		if ( file_exists( $cofigSample ) && ! file_exists( $cofig ) ) {
			copy( $cofigSample, $cofig );
		}
	}

}
