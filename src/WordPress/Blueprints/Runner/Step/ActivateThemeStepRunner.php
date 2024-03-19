<?php
/**
 * @file
 */

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\ActivateThemeStep;
use WordPress\Blueprints\Progress\Tracker;


class ActivateThemeStepRunner extends BaseStepRunner {

	public static function getStepClass(): string {
		return ActivateThemeStep::class;
	}

	/**
	 * @param ActivateThemeStep $input
	 * @return string|null
	 */
	public function getDefaultCaption( $input ) {
		return 'Activating theme ' . $input->slug;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\ActivateThemeStep $input
	 * @param \WordPress\Blueprints\Progress\Tracker                  $tracker
	 */
	public function run( $input, $tracker ) {
		// @TODO: Compare performance to the wp_activate_theme.php script.
		// On the first sight it seems to be significantly faster.
		return $this->getRuntime()->runShellCommand(
			array(
				'php',
				'wp-cli.phar',
				'--allow-root',
				'theme',
				'activate',
				$input->slug,
			)
		);

		// return $this->getRuntime()->evalPhpInSubProcess(
		// file_get_contents( __DIR__ . '/ActivatePlugin/wp_activate_theme.php' ),
		// [
		// 'THEME_FOLDER_NAME' => $this->input->themeFolderName,
		// ]
		// );
	}
}
