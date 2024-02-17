<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepHandler\BaseStepHandler;


class InstallPluginStepHandler extends BaseStepHandler {

	function execute( InstallPluginStep $input, Tracker $tracker = null ) {
		$tracker?->setCaption( $input->progress->caption ?? "Installing plugin " . $input->pluginZipFile );

		// @TODO: Measure the performance. It seems slow. Perhaps that's unzipping a large file, but it could also be spawning wp-cli
		return $this->resourceManager->bufferToTemporaryFile(
			$input->pluginZipFile,
			function ( $path ) use ( $input ) {
				return $this->getRuntime()->runShellCommand(
					[
						'php',
						'wp-cli.phar',
						'plugin',
						'install',
						$path,
						$input->options->activate ? '--activate' : '',
					]
				);
			},
			'.zip'
		);
	}
}
