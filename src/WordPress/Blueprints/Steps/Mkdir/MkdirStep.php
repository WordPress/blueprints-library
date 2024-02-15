<?php

namespace WordPress\Blueprints\Steps\Mkdir;

class MkdirStep {

	public function execute( MkdirStepInput $input ) {
		if ( is_dir( $input->path ) ) {
			return;
		}
		$success = mkdir( $input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to create directory at {$input->path}" );
		}
	}
}
