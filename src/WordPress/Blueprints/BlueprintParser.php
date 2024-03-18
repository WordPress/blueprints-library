<?php

namespace WordPress\Blueprints;

use InvalidArgumentException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use WordPress\Blueprints\Model\BlueprintBuilder;
use WordPress\Blueprints\Model\DataClass\Blueprint;

class BlueprintParser {

	/**
	 * @var BlueprintValidator
	 */
	protected $validator;

	/**
	 * @var BlueprintMapper
	 */
	protected $mapper;

	public function __construct(
		BlueprintValidator $validator,
		BlueprintMapper $mapper
	) {
		$this->validator = $validator;
		$this->mapper    = $mapper;
	}

	public function parse( $raw_blueprint ) {
		if ( $raw_blueprint instanceof \stdClass ) {
			return $this->fromObject( $raw_blueprint );
		}

		if ( is_string( $raw_blueprint ) ) {
			$data = json_decode( $raw_blueprint, false );

			if ( null === $data ) {
				throw new InvalidArgumentException( 'Malformed JSON.' );
			}

			return $this->fromObject( $data );
		}

		if ( $raw_blueprint instanceof Blueprint ) {
			return $this->fromBlueprint( $raw_blueprint );
		}

		if ( $raw_blueprint instanceof BlueprintBuilder ) {
			return $this->fromBlueprint( $raw_blueprint->toBlueprint() );
		}

		throw new InvalidArgumentException(
			'Unsupported $rawBlueprint type. Use a JSON string, a parsed JSON object, or a BlueprintBuilder instance.'
		);
	}

	/**
	 * @param \stdClass $data
	 */
	public function fromObject( $data ) {
		$result = $this->validator->validate( $data );
		if ( ! $result->isValid() ) {
			print_r( ( new ErrorFormatter() )->format( $result->error() ) );
			die();
		}
		return $this->mapper->map( $data );
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\Blueprint $blueprint
	 */
	public function fromBlueprint( $blueprint ) {
		$result = $this->validator->validate( $blueprint );
		// @TODO make the error understandable
		if ( ! $result->isValid() ) {
			print_r( ( new ErrorFormatter() )->format( $result->error() ) );
			die();
		}
		return $blueprint;
	}

	// private function findSubErrorForSpecificAnyOfOption(Error $e, string $anyOfRef)
	// {
	// if ($anyOfRef[0] === '#') {
	// $anyOfRef = substr($anyOfRef, 1);
	// }
	// if ($e->schemaPointers) {
	// foreach ($e->schemaPointers as $pointer) {
	// if (str_starts_with($pointer, $anyOfRef)) {
	// return $e;
	// }
	// }
	// }
	// if (!$e->subErrors) {
	// return $e->error;
	// }
	// foreach ($e->subErrors as $subError) {
	// $subError = findSubErrorForSpecificAnyOfOption($subError, $anyOfRef);
	// if ($subError !== null) {
	// return $subError;
	// }
	// }
	// return $e;
	// }

	// private function getSubschema($pointer)
	// {
	// if ($pointer[0] === '#') {
	// $pointer = substr($pointer, 1);
	// }
	// if ($pointer[0] !== '/') {
	// $pointer = substr($pointer, 1);
	// }
	// $path = explode('/', substr($pointer, 1));
	// $subSchema = $this->blueprintSchema;
	// foreach ($path as $key) {
	// if (is_numeric($key) && !property_exists($subSchema, $key)) {
	// foreach ($subSchema->anyOf as $v) {
	// if (is_object($v) && property_exists($v, '$ref')) {
	// $subSchema = $v;
	// break;
	// }
	// }
	// } else {
	// $subSchema = $subSchema->$key;
	// }
	// }
	//
	// return $subSchema;
	// }
}
