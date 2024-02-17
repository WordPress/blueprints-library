<?php
/**
 * @file
 */

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\InstallThemeStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepHandler\BaseStepHandler;


class InstallThemeStepHandler extends BaseStepHandler {
	/**
	 * @param InstallThemeStep $input
	 */
	function execute( InstallThemeStep $input, Tracker $tracker = null ) {
		$tracker?->setCaption( $input->progress->caption ?? "Installing theme " . $input->themeZipFile );

		// @TODO: Measure the performance. It seems slow. Perhaps that's unzipping a large file, but it could also be spawning wp-cli
		return $this->resourceManager->bufferToTemporaryFile(
			$input->themeZipFile,
			function ( $path ) use ( $input ) {
				return $this->getRuntime()->runShellCommand(
					[
						'php',
						'wp-cli.phar',
						'theme',
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
