<?php

namespace blueprints\src\WordPress\Blueprints\Steps\Unzip;

use blueprints\src\WordPress\Blueprints\Steps\BaseStep;
use WordPress\Zip\zipStreamReader;
use ZipArchive;
use function WordPress\Blueprints\Steps\Unzip\temp_path;

class UnzipStep extends BaseStep {

	public function __construct(
		public UnzipStepInput $input
	) {
		parent::__construct();
	}

	public static function getInputClass(): string {
		return UnzipStepInput::class;
	}

	public function execute() {
		$zipPath = temp_path( 'temp.zip' );
		$this->input->zipFile->saveTo( $zipPath );

		try {
			if ( ! is_dir( $this->input->toPath ) ) {
				mkdir( $this->input->toPath, 0777, true );
			}
			$zip = new ZipArchive();
			if ( $zip->open( $zipPath ) !== true ) {
				throw new \Exception( 'Failed to open zip file' );
			}
			$zip->extractTo( $this->input->toPath );
			$zip->close();
			chmod( $this->input->toPath, 0777 );
		} finally {
			unlink( $zipPath );
		}
	}
}
