<?php

namespace WordPress\Blueprints\Tests\Unit\Steps;

use PHPUnit\Framework\TestCase;
use WordPress\Blueprints\DataReference\InlineFile;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\Runtime;
use WordPress\Blueprints\Steps\ImportContentStep;
use WordPress\ByteStream\MemoryPipe;

class ImportContentStepTest extends TestCase {
	public function test_wxr_import_appends_call_inside_existing_php_script() {
		$runtime = new CapturingImportRuntime();
		$step    = $this->create_wxr_import_step();

		$step->run( $runtime, new Tracker() );

		$importer_script = file_get_contents( __DIR__ . '/../../../Steps/scripts/import-content.php' );
		$appended_code   = substr( $runtime->captured_code, strlen( $importer_script ) );

		$this->assertSame( 0, strpos( ltrim( $appended_code ), 'run_content_import([' ) );
		$this->assertStringNotContainsString( '<?php', $appended_code );
		$this->assertStringNotContainsString( '?>', $appended_code );
	}

	public function test_wxr_import_closes_output_stream() {
		$runtime = new CapturingImportRuntime();
		$step    = $this->create_wxr_import_step();

		$step->run( $runtime, new Tracker() );

		$this->assertTrue( $runtime->output_stream->reading_closed );
	}

	private function create_wxr_import_step() {
		$source = new InlineFile(
			array(
				'filename' => 'content.xml',
				'content'  => '<rss />',
			)
		);

		return new ImportContentStep(
			array(
				array(
					'type'   => 'wxr',
					'source' => $source,
				),
			)
		);
	}
}

class CapturingImportRuntime extends Runtime {
	/**
	 * @var string
	 */
	public $captured_code;
	/**
	 * @var TrackingMemoryPipe
	 */
	public $output_stream;

	public function __construct() {
		$this->output_stream = new TrackingMemoryPipe( '{"type":"completion"}' . "\n" );
	}

	public function create_php_sub_process(
		$code,
		$env = null,
		$input = null,
		$timeout = 60
	) {
		$this->captured_code = $code;

		return new SuccessfulImportProcess( $this->output_stream );
	}

	public function get_execution_context_root(): ?string {
		return null;
	}
}

class SuccessfulImportProcess {
	private $output_stream;

	public function __construct( MemoryPipe $output_stream ) {
		$this->output_stream = $output_stream;
	}

	public function start() {
	}

	public function getOutputStream( $pipe ) {
		return $this->output_stream;
	}

	public function getExitCode() {
		return 0;
	}

	public function stop() {
	}
}

class TrackingMemoryPipe extends MemoryPipe {
	public $reading_closed = false;

	public function close_reading(): void {
		$this->reading_closed = true;
		parent::close_reading();
	}
}
