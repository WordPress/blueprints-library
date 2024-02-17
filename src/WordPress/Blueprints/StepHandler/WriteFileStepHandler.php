<?php

namespace WordPress\Blueprints\StepHandler;

use WordPress\Blueprints\Model\DataClass\WriteFileStep;
use WordPress\Blueprints\Progress\Tracker;

class WriteFileStepHandler extends BaseStepHandler {
	public function execute(
		WriteFileStep $input,
		Tracker $progress = null
	) {
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
