<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\WriteFileStep;
use WordPress\Blueprints\Progress\Tracker;

class WriteFileStepRunner extends BaseStepRunner {
	public function run(
		WriteFileStep $input,
		Tracker $progress = null
	) {
		var_dump( $input->data );
		$path = $this->getRuntime()->resolvePath( $input->path );
		// @TODO: Treat $input->path as relative path to the document root (unless it's absolute)
		if ( is_string( $input->data ) ) {
			file_put_contents( $path, $input->data );

			return;
		}
		$fp2 = fopen( $path, 'w' );
		if ( $fp2 === false ) {
			throw new \Exception( "Failed to open file at {$path}" );
		}
		stream_copy_to_stream( $this->getResource( $input->data ), $fp2 );
		fclose( $fp2 );
	}

}
