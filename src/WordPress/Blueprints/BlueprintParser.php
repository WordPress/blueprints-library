<?php

namespace WordPress\Blueprints;

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

	function remove_utf8_bom( $text ) {
		$bom  = pack( 'H*', 'EFBBBF' );
		$text = preg_replace( "/^$bom/", '', $text );
		return $text;
	}

	public function parse( $rawBlueprint ) {
		if ( $rawBlueprint instanceof \stdClass ) {
			return $this->fromObject( $rawBlueprint );
		}
		if ( is_string( $rawBlueprint ) ) {
			$data = json_decode( $rawBlueprint, false);

			if ( null === $data ) {
				throw new InvalidArgumentException( 'Malformed JSON.' );
			}

			return $this->fromObject( $data );
		}

		if ( $rawBlueprint instanceof Blueprint ) {
			return $this->fromBlueprint( $rawBlueprint );
		}

		if ( $rawBlueprint instanceof BlueprintBuilder ) {
			return $this->fromBlueprint( $rawBlueprint->toBlueprint() );
		}

		throw new InvalidArgumentException(
			'Unsupported $rawBlueprint type. Use a JSON string, a parsed JSON object, or a BlueprintBuilder instance.'
		);
	}

	public function fromObject( object $data ) {
		$result = $this->validator->validate( $data );
		if ( ! $result->isValid() ) {
			print_r( ( new ErrorFormatter() )->format( $result->error() ) );
			die();
		}
		return $this->mapper->map( $data );
	}

	public function fromBlueprint( Blueprint $blueprint ) {
		$result = $this->validator->validate( $blueprint );
		if ( ! $result->isValid() ) {
			print_r( ( new ErrorFormatter() )->format( $result->error() ) );
			die();
		}
		return $blueprint;
	}

//    private function findSubErrorForSpecificAnyOfOption(Error $e, string $anyOfRef)
//    {
//        if ($anyOfRef[0] === '#') {
//            $anyOfRef = substr($anyOfRef, 1);
//        }
//        if ($e->schemaPointers) {
//            foreach ($e->schemaPointers as $pointer) {
//                if (str_starts_with($pointer, $anyOfRef)) {
//                    return $e;
//                }
//            }
//        }
//        if (!$e->subErrors) {
//            return $e->error;
//        }
//        foreach ($e->subErrors as $subError) {
//            $subError = findSubErrorForSpecificAnyOfOption($subError, $anyOfRef);
//            if ($subError !== null) {
//                return $subError;
//            }
//        }
//        return $e;
//    }

//    private function getSubschema($pointer)
//    {
//        if ($pointer[0] === '#') {
//            $pointer = substr($pointer, 1);
//        }
//        if ($pointer[0] !== '/') {
//            $pointer = substr($pointer, 1);
//        }
//        $path = explode('/', substr($pointer, 1));
//        $subSchema = $this->blueprintSchema;
//        foreach ($path as $key) {
//            if (is_numeric($key) && !property_exists($subSchema, $key)) {
//                foreach ($subSchema->anyOf as $v) {
//                    if (is_object($v) && property_exists($v, '$ref')) {
//                        $subSchema = $v;
//                        break;
//                    }
//                }
//            } else {
//                $subSchema = $subSchema->$key;
//            }
//        }
//
//        return $subSchema;
//    }
}
