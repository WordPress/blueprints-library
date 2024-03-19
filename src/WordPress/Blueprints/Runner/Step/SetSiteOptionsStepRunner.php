<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep;
use WordPress\Blueprints\Progress\Tracker;


class SetSiteOptionsStepRunner extends BaseStepRunner {
	/**
	 * @param SetSiteOptionsStep                     $input
	 * @param \WordPress\Blueprints\Progress\Tracker $tracker
	 */
	function run( $input, $tracker ) {
		// Running a custom PHP script is much faster than setting each option
		// with a separate wp-cli command.
		return $this->getRuntime()->evalPhpInSubProcess(
			'
<?php
		require getenv(\'DOCROOT\'). \'/wp-load.php\';
		$site_options = getenv("OPTIONS") ? json_decode(getenv("OPTIONS"), true) : [];
		foreach($site_options as $name => $value) {
			update_option($name, $value);
		}
',
			array(
				'OPTIONS' => json_encode( $input->options ),
			)
		);
	}

	public function getDefaultCaption( $input ) {
		return 'Setting site options';
	}
}
