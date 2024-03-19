<?php
/**
 * @file
 */

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\RunSQLStep;
use WordPress\Blueprints\Progress\Tracker;


class RunSQLStepRunner extends BaseStepRunner {
	/**
	 * @param RunSQLStep                                  $input
	 * @param \WordPress\Blueprints\Progress\Tracker|null $progress
	 */
	function run(
		$input,
		$progress = null
	) {
		return $this->getRuntime()->evalPhpInSubProcess(
			<<<'CODE'
<?php
		require_once getenv("DOCROOT") . '/wp-load.php';

		$handle = STDIN;
		$buffer = '';

		global $wpdb;
		while ($bytes = fgets($handle)) {
			$buffer .= $bytes;

			if (!feof($handle) && substr($buffer, -1, 1) !== "\n") {
				continue;
			}

			$wpdb->query($buffer);
			$buffer = '';
		}
		fclose($handle);
CODE
			,
			null,
			$this->getResource( $input->sql )
		);
	}

	public function getDefaultCaption( $input ) {
		return 'Running SQL queries';
	}
}
