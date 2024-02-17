<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\ActivatePluginStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepHandler\BaseStepHandler;


class ActivatePluginStepHandler extends BaseStepHandler {

	function execute( ActivatePluginStep $input, Tracker $tracker = null ) {
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
}
