<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep;
use WordPress\Blueprints\Progress\Tracker;

class RunWordPressInstallerStepRunner extends BaseStepRunner {
	function run( RunWordPressInstallerStep $input, Tracker $tracker ) {
		$tracker?->setCaption( "Setting site options" );

		return $this->getRuntime()->runShellCommand(
			[
				'php',
				'wp-cli.phar',
				'core',
				'install',
				'--url=http://localhost:8080',
				'--title=Playground Site',
				'--admin_user=' . $input->options->adminUsername,
				'--admin_password=' . $input->options->adminPassword,
				'--admin_email=admin@wordpress.internal',
			],
			$this->getRuntime()->getDocumentRoot()
		);
	}
}
