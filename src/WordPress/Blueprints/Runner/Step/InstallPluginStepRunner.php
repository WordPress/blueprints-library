<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use WordPress\Blueprints\Progress\Tracker;


class InstallPluginStepRunner extends InstallAssetStepRunner {

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\InstallPluginStep $input
	 * @param \WordPress\Blueprints\Progress\Tracker                  $tracker
	 */
	function run( $input, $tracker ) {
		// @TODO: inject this information into this step
		$pluginDir  = 'plugin' . rand( 0, 1000 );
		$targetPath = $this->getRuntime()->resolvePath( 'wp-content/plugins/' . $pluginDir );
		$this->unzipAssetTo( $input->pluginZipFile, $targetPath );

		if ( $input->activate ) {
			// Don't use wp-cli for activation as it expects a slug, and, in the general case,
			// plugins in WordPress are identified by their path, not slug.
			$this->getRuntime()->evalPhpInSubProcess(
				file_get_contents( __DIR__ . '/ActivatePlugin/wp_activate_plugin.php' ),
				array(
					'PLUGIN_PATH' => $targetPath,
				)
			);
		}
	}

	public function getDefaultCaption( $input ) {
		return 'Installing plugin ' . $input->pluginZipFile;
	}
}
