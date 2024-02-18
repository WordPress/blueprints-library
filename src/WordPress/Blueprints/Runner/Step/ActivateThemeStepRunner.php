<?php
/**
 * @file
 */

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\ActivateThemeStep;
use WordPress\Blueprints\Progress\Tracker;


class ActivateThemeStepRunner extends BaseStepRunner {

	static public function getStepClass(): string {
		return ActivateThemeStep::class;
	}

	/**
	 * @param ActivateThemeStep $input
	 */
	protected function getDefaultCaption( $input ): string|null {
		return "Activating theme " . $input->slug;
	}

	public function run( ActivateThemeStep $input, Tracker $tracker ) {
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
