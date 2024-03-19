<?php
/**
 * @file
 */

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\InstallThemeStep;
use WordPress\Blueprints\Progress\Tracker;


class InstallThemeStepRunner extends InstallAssetStepRunner {
	/**
	 * @param InstallThemeStep                       $input
	 * @param \WordPress\Blueprints\Progress\Tracker $tracker
	 */
	function run( $input, $tracker ) {
		// @TODO: inject this information into this step
		$themeDir   = 'theme' . rand( 0, 1000 );
		$targetPath = $this->getRuntime()->resolvePath( 'wp-content/themes/' . $themeDir );
		$this->unzipAssetTo( $input->themeZipFile, $targetPath );

		if ( $input->activate ) {
			// Don't use wp-cli for activation as it expects a slug, and, in the general case,
			// plugins in WordPress are identified by their path, not slug.
			$this->getRuntime()->evalPhpInSubProcess(
				file_get_contents( __DIR__ . '/ActivateTheme/wp_activate_theme.php' ),
				array(
					'THEME_FOLDER_NAME' => $themeDir,
				)
			);
		}
	}

	public function getDefaultCaption( $input ) {
		return 'Installing theme ' . $input->themeZipFile;
	}
}
