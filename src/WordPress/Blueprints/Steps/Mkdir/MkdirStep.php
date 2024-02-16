<?php

namespace WordPress\Blueprints\Steps\Mkdir;

use Symfony\Component\Validator\Constraints as Assert;

use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedClass;

class MkdirStep {

	public function execute( MkdirStepDefinition $input ) {
		if ( is_dir( $input->path ) ) {
			return;
		}
		$success = mkdir( $input->path );
		if ( ! $success ) {
			throw new \Exception( "Failed to create directory at {$input->path}" );
		}
	}
}
