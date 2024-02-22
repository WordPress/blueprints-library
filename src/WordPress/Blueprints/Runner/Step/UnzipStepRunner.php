<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\UnzipStep;
use WordPress\Blueprints\Progress\Tracker;
use function WordPress\Zip\zip_extract_to;

class UnzipStepRunner extends BaseStepRunner {

	public function run(
		UnzipStep $input,
        // maybe publish events for tracking instead of passing a variable?
        // so that the tracker is a singleton with listeners
		Tracker $tracker = null
	) {
        if (!is_null($tracker)) {
            $tracker->set( 10, 'Unzipping...' );
        }

		// @TODO: Expose a generic helper method for this, e.g. $this->getExecutionContext()->resolvePath($input->extractToPath);
		$toPath = $this->getRuntime()->getDocumentRoot() . '/' . $input->extractToPath;
		zip_extract_to( $this->getResource( $input->zipFile ), $toPath );
	}

}
