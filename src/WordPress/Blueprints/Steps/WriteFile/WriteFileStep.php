<?php

namespace WordPress\Blueprints\Steps\WriteFile;

use WordPress\Blueprints\Progress\Tracker;

class WriteFileStep {

	public function execute(
		WriteFileStepDefinition $input,
		?Tracker $progress = null
	) {
		$fp2 = fopen( $input->toPath, 'w' );
		stream_copy_to_stream( $input->file, $fp2 );
		fclose( $fp2 );
	}

}
