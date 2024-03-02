<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\UnzipStep;
use WordPress\Blueprints\Progress\Tracker;
use function WordPress\Zip\zip_extract_to;

class UnzipStepRunner extends BaseStepRunner {

	/**
	 * Runs the Unzip Step
	 *
	 * @param UnzipStep $input Step.
	 * @param Tracker   $progress_tracker Tracker.
	 * @return void
	 */
	public function run(
		UnzipStep $input,
		Tracker $progress_tracker
	) {
		$progress_tracker->set( 10, 'Unzipping...' );

		$resolved_to_path = $this->getRuntime()->resolvePath( $input->extractToPath );
		zip_extract_to( $this->getResource( $input->zipFile ), $resolved_to_path );
	}
}
