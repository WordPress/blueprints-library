<?php

namespace WordPress\Blueprints\Steps\Unzip;

use function WordPress\Zip\zip_extract_to;

class UnzipStep {

	public function execute(
		UnzipStepInput $input
	) {
		zip_extract_to( $input->zipFile, $input->toPath );
	}

}
