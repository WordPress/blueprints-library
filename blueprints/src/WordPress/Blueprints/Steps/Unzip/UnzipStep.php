<?php

namespace WordPress\Blueprints\Steps\Unzip;

use WordPress\Blueprints\Steps\BaseStep;
use WordPress\Zip\zipStreamReader;

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
		while ( $entry = ZipStreamReader::readEntry( $this->input->zipFile ) ) {
			if ( ! $entry->isFileEntry() ) {
				continue;
			}
			$path   = $this->input->toPath . '/' . sanitize_path( $entry->path );
			$parent = dirname( $path );
			if ( ! is_dir( $parent ) ) {
				mkdir( $parent, 0777, true );
			}

			if ( $entry->isDirectory ) {
				if ( ! is_dir( $path ) ) {
					mkdir( $path, 0777, true );
				}
			} else {
				file_put_contents( $path, $entry->bytes );
			}
		}
	}
}

// @TODO: Find a more reliable technique
function sanitize_path( $path ) {
	if ( empty( $path ) ) {
		return '';
	}

	return preg_replace( '#/\.+(/|$)#', '/', $path );
}
