<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use WordPress\Blueprints\Progress\Tracker;
use function WordPress\Zip\zip_extract_to;


class InstallPluginStepRunner extends BaseStepRunner {

	function run( InstallPluginStep $input, Tracker $tracker ) {
		$tracker?->setCaption( $input->progress->caption ?? "Installing plugin" );
		$toPath = $this->getRuntime()->getDocumentRoot() . '/wp-content/plugins';

		$pluginsBefore = glob( $toPath . '/*', GLOB_ONLYDIR );
		zip_extract_to( $this->getResource( $input->pluginZipFile ), $toPath );
		$pluginsAfter = glob( $toPath . '/*', GLOB_ONLYDIR );
		$pluginDir    = array_values( array_diff( $pluginsAfter, $pluginsBefore ) )[0];

		$this->getRuntime()->evalPhpInSubProcess(
			file_get_contents( __DIR__ . '/ActivatePlugin/wp_activate_plugin.php' ),
			[
				'PLUGIN_PATH' => $pluginDir, //$toPath . '/' . $input->pluginSlug,
			]
		);

		// @TODO: Measure the performance. It seems slow. Perhaps that's unzipping a large file, but it could also be spawning wp-cli
//		return $this->resourceManager->bufferToTemporaryFile(
//			$input->pluginZipFile,
//			function ( $path ) use ( $input ) {
//				return $this->getRuntime()->runShellCommand(
//					[
//						'php',
//						'wp-cli.phar',
//						'plugin',
//						'install',
//						$path,
//						$input->activate ? '--activate' : '',
//					]
//				);
//			},
//			'.zip'
//		);
	}
}
