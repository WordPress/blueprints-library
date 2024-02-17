<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\UnzipStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepHandler\BaseStepHandler;
use function WordPress\Zip\zip_extract_to;

class UnzipStepHandler extends BaseStepHandler {

	public function execute(
		UnzipStep $input,
		Tracker $progress = null
	) {
		$progress?->set( 10, 'Unzipping...' );
		zip_extract_to( $this->getResource( $input->zipFile ), $input->extractToPath );
	}

}
