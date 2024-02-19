<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\UnzipStep;
use WordPress\Blueprints\Progress\Tracker;
use function WordPress\Zip\zip_extract_to;

class UnzipStepRunner extends BaseStepRunner {

	public function run(
		UnzipStep $input,
		Tracker $progress = null
	) {
		$progress?->set( 10, 'Unzipping...' );
		$toPath = $this->getRuntime()->getDocumentRoot() . '/' . $input->extractToPath;

		$this->resourceManager->bufferToTemporaryFile(
			$input->zipFile,
			function ( $path ) use ( $toPath ) {
				$zip = new \ZipArchive();
				$zip->open( $path );
				$zip->extractTo( $toPath );
				$zip->close();
			},
			'.zip'
		);
		// Stream-extracting with zip_extract_to is much slower than
		// buffering the entire download and then extracting it using
		// the native PHP ZipArchive class.
		// zip_extract_to( $this->getResource( $input->zipFile ), $toPath );
	}

}
