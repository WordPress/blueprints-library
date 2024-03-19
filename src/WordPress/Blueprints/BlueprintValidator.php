<?php

namespace WordPress\Blueprints;

use Opis\JsonSchema\{Helper, Validator, ValidationResult};
use WordPress\Blueprints\Model\DataClass\Blueprint;

class BlueprintValidator {

	const SCHEMA_ID = 'https://playground.wordpress.net/blueprint-schema.json';

	protected $validator;

	public function __construct( $jsonSchemaPath ) {
		$this->validator = new Validator();
		$this->validator->resolver()->registerFile(
			self::SCHEMA_ID,
			$jsonSchemaPath
		);
	}

	public function validate( $input ) {
		/**
		 * Casts a Blueprint object to a stdClass object and recursively
		 * removes any null values.
		 *
		 * Why?
		 *
		 * Blueprint JSON Schema describes some properties as non-required.
		 * PHP class properties, however, are always there and the missing
		 * non-required properties are set to null.
		 *
		 * JSON Schema validation can handle missing properties, but it does not
		 * allow setting, e.g., a non-required string property to null.
		 *
		 * This null removal is a workaround for this issue. Ideally, we would
		 * have a way of validating the Blueprint instance directly.
		 */
		if ( is_object( $input ) && $input instanceof Blueprint ) {
			$input = $this->removeNulls( json_decode( json_encode( $input ) ) );
		}

		return $this->validator->validate( $input, self::SCHEMA_ID );
	}

	protected function removeNulls( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $subValue ) {
				if ( $subValue === null ) {
					unset( $value[ $key ] );
				}
				$value[ $key ] = $this->removeNulls( $subValue );
			}
		} elseif ( is_object( $value ) ) {
			foreach ( get_object_vars( $value ) as $key => $subValue ) {
				if ( $subValue === null ) {
					unset( $value->$key );
				} else {
					$value->$key = $this->removeNulls( $subValue );
				}
			}
		}

		return $value;
	}
}
