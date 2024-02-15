<?php

namespace WordPress\Blueprints\Parsing;

use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\ConstraintViolation;

class Parser {

	public function __construct(
		/** @var callable */
		private $formFactory,
	) {
	}

	public function parse( string $blueprint_json ) {
		$factory         = $this->formFactory;
		$form            = $factory();
		$blueprint_array = json_decode( $blueprint_json, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new ParsingException( [
				new ConstraintViolation( 'Invalid JSON', null, [], '', 'blueprint', $blueprint_json ),
			] );
		}
		$form->submit( $blueprint_array );

		if ( ! $form->isSubmitted() || ! $form->isValid() ) {
			$errors = [];
			foreach ( $form->getErrors( true, true ) as $error ) {
				$key = static::get_error_path( $error );
				/** @var $error FormError */
				$errors[ $key ] = $error->getMessage() . " – " . implode( ", ", $error->getMessageParameters() );
			}
			throw new ParsingException( $errors );
		}

		return $form->getData();
	}

	static private function get_error_path( FormError $error ) {
		$origin = $error->getOrigin();
		$path   = [];
		while ( $origin ) {
			$path[] = $origin->getName();
			$origin = $origin->getParent();
		}

		return implode( '.', array_reverse( $path ) );
	}

}
