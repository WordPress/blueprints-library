<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\ActivatePluginStep;
use WordPress\Blueprints\Progress\Tracker;


class ActivatePluginStepRunner extends BaseStepRunner {

	function run( ActivatePluginStep $input, Tracker $tracker ) {
		$tracker?->setCaption( $input->progress->caption ?? "Activating plugin " . $input->slug );

		// @TODO: Compare performance to the wp_activate_plugin.php script.
		//        On the first sight it seems to be significantly faster.
		return $this->getRuntime()->runShellCommand(
			[
				'php',
				'wp-cli.phar',
				'plugin',
				'activate',
				$input->slug,
			]
		);

//		return $this->getRuntime()->evalPhpInSubProcess(
//			file_get_contents( __DIR__ . '/ActivatePlugin/wp_activate_plugin.php' ),
//			[
//				'PLUGIN_PATH' => $input->pluginPath,
//			]
//		);
	}

	public function getDefaultCaption( $input ): null|string {
		return "Activating plugin";
	}
}
