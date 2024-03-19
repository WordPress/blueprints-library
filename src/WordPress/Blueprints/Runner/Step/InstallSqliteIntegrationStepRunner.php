<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\InstallSqliteIntegrationStep;
use WordPress\Blueprints\Progress\Tracker;


class InstallSqliteIntegrationStepRunner extends InstallAssetStepRunner {
	/**
	 * @param InstallSqliteIntegrationStep $input
	 * @param Tracker                      $tracker
	 */
	function run( $input, $tracker ) {
		$pluginDir  = 'sqlite-database-integration';
		$targetPath = $this->getRuntime()->resolvePath( 'wp-content/mu-plugins/' . $pluginDir );
		$this->unzipAssetTo( $input->sqlitePluginZip, $targetPath );

		// Setup the SQLite integration plugin
		$db = file_get_contents( $this->getRuntime()->resolvePath( 'wp-content/mu-plugins/sqlite-database-integration/db.copy' ) );
		$db = str_replace(
			"'{SQLITE_IMPLEMENTATION_FOLDER_PATH}'",
			"__DIR__.'/mu-plugins/sqlite-database-integration/'",
			$db
		);
		$db = str_replace(
			"'{SQLITE_PLUGIN}'",
			"__DIR__.'/mu-plugins/sqlite-database-integration/load.php'",
			$db
		);
		file_put_contents( $this->getRuntime()->resolvePath( 'wp-content/db.php' ), $db );
		file_put_contents(
			$this->getRuntime()->resolvePath( 'wp-content/mu-plugins/0-sqlite.php' ),
			'<?php require_once __DIR__ . "/sqlite-database-integration/load.php"; '
		);
	}

	public function getDefaultCaption( $input ) {
		return 'Installing SQLite integration plugin';
	}
}
