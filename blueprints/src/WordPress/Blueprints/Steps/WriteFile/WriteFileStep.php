<?php

namespace WordPress\Blueprints\Steps\WriteFile;

class WriteFileStep {

	public function execute(
		WriteFileStepInput $input
	) {
		$fp2 = fopen( $input->toPath, 'w' );
		stream_copy_to_stream( $input->file, $fp2 );
		fclose( $fp2 );
	}

}
