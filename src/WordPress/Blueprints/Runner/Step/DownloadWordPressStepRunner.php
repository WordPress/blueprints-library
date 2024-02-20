<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\DownloadWordPressStep;
use WordPress\Blueprints\Progress\Tracker;

class DownloadWordPressStepRunner extends InstallAssetStepRunner {

	public function run(
		DownloadWordPressStep $input,
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
