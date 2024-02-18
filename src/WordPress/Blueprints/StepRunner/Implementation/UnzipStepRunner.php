<?php

namespace WordPress\Blueprints\StepRunner\Implementation;

use WordPress\Blueprints\Model\DataClass\UnzipStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepRunner\BaseStepRunner;
use function WordPress\Zip\zip_extract_to;

class UnzipStepRunner extends BaseStepRunner {

	public function run(
		UnzipStep $input,
		Tracker $progress = null
	) {
		$progress?->set( 10, 'Unzipping...' );
		zip_extract_to( $this->getResource( $input->zipFile ), $input->extractToPath );
	}

}
