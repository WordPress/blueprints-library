<?php
/**
 * @file
 */

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\ActivateThemeStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepHandler\BaseStepHandler;


class ActivateThemeStepHandler extends BaseStepHandler {
	function execute( ActivateThemeStep $input, Tracker $tracker = null ) {
		$tracker?->setCaption( $input->progress->caption ?? "Activating theme " . $input->slug );

		// @TODO: Compare performance to the wp_activate_theme.php script.
		//        On the first sight it seems to be significantly faster.
		return $this->getRuntime()->runShellCommand(
			[
				'php',
				'wp-cli.phar',
				'theme',
				'activate',
				$input->slug,
			]
		);

//		return $this->getRuntime()->evalPhpInSubProcess(
//			file_get_contents( __DIR__ . '/ActivatePlugin/wp_activate_theme.php' ),
//			[
//				'THEME_FOLDER_NAME' => $this->input->themeFolderName,
//			]
//		);
	}
}
