<?php

namespace WordPress\Blueprints\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class BlueprintCliTest extends TestCase {
	public function test_logger_does_not_corrupt_jsonl_output() {
		$cli_path = realpath( __DIR__ . '/../../bin/blueprint.php' );
		$probe    = str_replace(
			'CLI_PATH',
			var_export( $cli_path, true ),
			<<<'PHP'
$_SERVER['argv'] = array( 'blueprint.php', 'help' );
ob_start();
require CLI_PATH;
ob_end_clean();

$config = cliArgsToRunnerConfiguration(
	array( './blueprint.json' ),
	array(
		'mode'                        => 'apply-to-existing-site',
		'site-path'                   => sys_get_temp_dir(),
		'site-url'                    => 'http://example.com',
		'truncate-new-site-directory' => false,
		'db-engine'                   => 'sqlite',
	)
);
$config->get_logger()->warning( 'diagnostic probe' );
echo json_encode( array( 'type' => 'completion' ) ) . "\n";
PHP
		);
		$probe_path = tempnam( sys_get_temp_dir(), 'blueprint-cli-test-' );
		file_put_contents( $probe_path, "<?php\n" . $probe );

		try {
			$process = new Process( array( PHP_BINARY, $probe_path ) );
			$process->run();

			$this->assertSame( 0, $process->getExitCode(), $process->getErrorOutput() );
			$this->assertSame( array( 'type' => 'completion' ), json_decode( trim( $process->getOutput() ), true ) );
			$this->assertStringContainsString( '[warning] diagnostic probe', $process->getErrorOutput() );
		} finally {
			unlink( $probe_path );
		}
	}
}
