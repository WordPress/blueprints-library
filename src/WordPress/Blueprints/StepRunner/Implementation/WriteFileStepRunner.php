<?php

namespace WordPress\Blueprints\StepRunner\Implementation;

use WordPress\Blueprints\Model\DataClass\WriteFileStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepRunner\BaseStepRunner;

class WriteFileStepRunner extends BaseStepRunner {
	public function run(
		WriteFileStep $input,
		Tracker $progress = null
	) {
		// @TODO: Treat $input->path as relative path to the document root (unless it's absolute)
		if ( is_string( $input->data ) ) {
			file_put_contents( $input->path, $input->data );

			return;
		}
		$fp2 = fopen( $input->path, 'w' );
		if ( $fp2 === false ) {
			throw new \Exception( "Failed to open file at {$input->path}" );
		}
		stream_copy_to_stream( $this->getResource( $input->data ), $fp2 );
		fclose( $fp2 );
	}

}
