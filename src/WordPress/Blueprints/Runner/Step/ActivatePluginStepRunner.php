<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\ActivatePluginStep;
use WordPress\Blueprints\Progress\Tracker;


class ActivatePluginStepRunner extends BaseStepRunner {

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\ActivatePluginStep $input
	 * @param \WordPress\Blueprints\Progress\Tracker                   $tracker
	 */
	function run( $input, $tracker ) {
		( $nullsafeVariable1 = $tracker ) ? $nullsafeVariable1->setCaption( $input->progress->caption ?? 'Activating plugin ' . $input->slug ) : null;

		// @TODO: Compare performance to the wp_activate_plugin.php script.
		// On the first sight it seems to be significantly faster.
		return $this->getRuntime()->runShellCommand(
			array(
				'php',
				'wp-cli.phar',
				'--allow-root',
				'plugin',
				'activate',
				$input->slug,
			)
		);

		// return $this->getRuntime()->evalPhpInSubProcess(
		// file_get_contents( __DIR__ . '/ActivatePlugin/wp_activate_plugin.php' ),
		// [
		// 'PLUGIN_PATH' => $input->pluginPath,
		// ]
		// );
	}

	public function getDefaultCaption( $input ) {
		return 'Activating plugin';
	}
}
