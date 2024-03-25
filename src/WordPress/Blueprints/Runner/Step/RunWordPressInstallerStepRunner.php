<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep;
use WordPress\Blueprints\Progress\Tracker;

class RunWordPressInstallerStepRunner extends BaseStepRunner {
	/**
	 * @param \WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep $input
	 * @param \WordPress\Blueprints\Progress\Tracker                          $tracker
	 */
	function run( $input, $tracker ) {
		$result = $this->getRuntime()->runShellCommand(
			array(
				'php',
				'wp-cli.phar',
				'--allow-root',
				'core',
				'install',
				'--url=http://localhost:8081',
				'--title=Playground Site',
				'--admin_user=' . $input->options->adminUsername,
				'--admin_password=' . $input->options->adminPassword,
				'--admin_email=admin@wordpress.internal',
			),
			$this->getRuntime()->getDocumentRoot()
		);
		return trim( $result );
	}

	public function getDefaultCaption( $input ) {
		return 'Installing WordPress';
	}
}
