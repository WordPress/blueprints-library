<?php

namespace WordPress\Blueprints\Steps\Unzip;

use WordPress\Blueprints\Progress\Tracker;
use function WordPress\Zip\zip_extract_to;

class UnzipStep {

	public function execute(
		UnzipStepInput $input,
		Tracker $progress = null
	) {
		$progress?->set( 10, 'Unzipping...' );
		zip_extract_to( $input->zipFile, $input->toPath );
	}

}
