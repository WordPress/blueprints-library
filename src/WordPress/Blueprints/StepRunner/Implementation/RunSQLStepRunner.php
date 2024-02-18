<?php
/**
 * @file
 */

namespace WordPress\Blueprints\StepRunner\Implementation;

use WordPress\Blueprints\Model\DataClass\RunSQLStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepRunner\BaseStepRunner;


class RunSQLStepRunner extends BaseStepRunner {
	/**
	 * @param RunSQLStep $input
	 */
	function run(
		RunSQLStep $input,
		Tracker $progress = null
	) {
		$progress?->setCaption( $input->progress->caption ?? "Running SQL queries" );

		return $this->getRuntime()->evalPhpInSubProcess( <<<'CODE'
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
CODE,
			null,
			$this->getResource( $input->sql )
		);
	}
}