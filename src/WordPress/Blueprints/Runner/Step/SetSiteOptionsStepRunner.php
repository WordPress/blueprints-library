<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep;
use WordPress\Blueprints\Progress\Tracker;


class SetSiteOptionsStepRunner extends BaseStepRunner {
	/**
	 * @param SetSiteOptionsStep $input
	 */
	function run( SetSiteOptionsStep $input, Tracker $tracker ) {
		$tracker?->setCaption( "Setting site options" );

		// Running a custom PHP script is much faster than setting each option
		// with a separate wp-cli command.
		return $this->getRuntime()->evalPhpInSubProcess( <<<'CODE'
<?php
		require getenv("DOCROOT") . '/wp-load.php';
		$site_options = getenv("OPTIONS") ? json_decode(getenv("OPTIONS"), true) : [];
		foreach($site_options as $name => $value) {
			update_option($name, $value);
		}
CODE,
			[
				'OPTIONS' => json_encode( get_object_vars( $input->options ) ),
			]
		);
	}
}
