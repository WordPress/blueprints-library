<?php

namespace WordPress\Blueprints;

use Swaggest\JsonSchema\InvalidValue;
use Swaggest\JsonSchema\Structure\ClassStructureContract;
use WordPress\Blueprints\Model\BlueprintComposer;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;
use WordPress\Blueprints\Resource\Resolver\ResourceResolverInterface;
use function WordPress\Blueprints\Model\findSubErrorForSpecificAnyOfOption;

class BlueprintParser {

	public function __construct( protected ResourceResolverInterface $resourceResolver, protected $blueprintSchema ) {
	}

	public function parse( string|object $rawBlueprint ) {
		if ( is_string( $rawBlueprint ) ) {
			$rawBlueprint = json_decode( $rawBlueprint, false );
		}

		if ( $rawBlueprint instanceof \stdClass ) {
			$rawBlueprint = BlueprintBuilder::import( $rawBlueprint );
		}

		if ( $rawBlueprint instanceof BlueprintComposer ) {
			$rawBlueprint = $rawBlueprint->getBuilder();
		}

		if ( ! ( $rawBlueprint instanceof BlueprintBuilder ) ) {
			throw new \InvalidArgumentException( 'Unsupported $rawBlueprint type. Use a JSON string, a parsed JSON object, a BlueprintComposer instance, or a BlueprintBuilder instance.' );
		}

		return $this->parseBuilder( $rawBlueprint );
	}

	protected function parseBuilder( BlueprintBuilder $builder ) {
		$this->replaceUrlsWithFileReferenceDataObjects( $builder );

		try {
			$builder->validate();
		} catch ( InvalidValue $rootError ) {
//			$errorReport = [
//				"dataPointer"   => $rootError->getDataPointer(),
//				"schemaPointer" => $rootError->getSchemaPointer(),
//				"Message"       => $rootError->getMessage(),
//				"Data"          => $rootError->data,
//				"Constraint"    => $rootError->constraint,
//			];

			$specificError = $this->getSpecificAnyOfError( $rootError );
			if ( $specificError ) {
				throw $specificError;
			}
			throw $rootError;
		}

		return $builder->toDataObject();
	}

	/**
	 * Resolves resource declared with shorthand URLs with their
	 * full data object definition.
	 *
	 * For example, the following Blueprint:
	 *
	 * {
	 *     "staps": [
	 *          {
	 *              "step": "unzip",
	 *              "toPath": "plugins/gutenberg",
	 *              "data": "https://plugins.wordpress.org/gutenberg.zip"
	 *          }
	 *      ]
	 * }
	 *
	 * Would be transformed to:
	 *
	 * {
	 *      "staps": [
	 *           {
	 *               "step": "unzip",
	 *               "toPath": "plugins/gutenberg",
	 *               "data": {
	 *                   "resource": "url",
	 *                   "url": "https://plugins.wordpress.org/gutenberg.zip"
	 *               }
	 *           }
	 *       ]
	 *  }
	 */
	protected function replaceUrlsWithFileReferenceDataObjects( $builder ) {
		if ( ! ( $builder instanceof ClassStructureContract ) || $builder instanceof WriteFileStep ) {
			return;
		}
		foreach ( $builder::schema()->getProperties() as $key => $value ) {
			if ( is_string( $builder->$key ) ) {
				if ( $builder::schema()->getProperty( $key )->getFromRef() == '#/definitions/FileReference' ) {
					$resource = $this->resourceResolver->parseUrl( $builder->$key );
					if ( false === $resource ) {
						throw new \InvalidArgumentException( "Could not parse resource {$builder->$key}" );
					}
					$builder->$key = $resource;
				}
			} elseif ( is_object( $builder->$key ) ) {
				$this->replaceUrlsWithFileReferenceDataObjects( $builder->$key );
			} elseif ( is_array( $builder->$key ) ) {
				foreach ( $builder->$key as $k => $v ) {
					$this->replaceUrlsWithFileReferenceDataObjects( $v );
				}
			}
		}
	}

	/**
	 * Narrows down ambiguous anyOf errors using the discriminator property.
	 *
	 * When one of the `anyOf` inputs doesn't match the schema, Swaggest\JsonSchema will return as many errors,
	 * as there are `anyOf` options. Sometimes that means 26 errors to sieve through. For example:
	 *
	 * ```
	 *  No valid results for oneOf {
	 *  0: Enum failed, enum: ["a"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[0]
	 *  1: Enum failed, enum: ["b"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[1]
	 *  2: No valid results for anyOf {
	 *  0: Enum failed, enum: ["c"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]->anyOf[0]
	 *  1: Enum failed, enum: ["d"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]->anyOf[1]
	 *  2: Enum failed, enum: ["e"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]->anyOf[2]
	 *  } at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]
	 *  } at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo
	 * ```
	 *
	 * It's highly impractical to reason about that much output, so we narrow down the error to the specific `anyOf` option that failed.
	 *
	 * This function uses the discriminator property to find the specific `anyOf` option that failed.
	 * For example, if the data looks like this:
	 *
	 * ```
	 * {
	 *     "steps": [
	 *          { "step": "activatePlugin", "plugin": null },
	 *       ]
	 * }
	 * ```
	 *
	 * And the schema looks like this:
	 *
	 * ```
	 * "StepDefinition": {
	 *     "type": "object",
	 *     "discriminator": {
	 *         "propertyName": "step"
	 *     },
	 *     "oneOf": [
	 *         { "$ref": "#/definitions/ActivatePluginStep" },
	 *         { "$ref": "#/definitions/ActivateThemeStep" },
	 * ```
	 *
	 * This function will go through all the errors reported by Swaggest\JsonSchema and find the one associated
	 * with the `ActivatePluginStep` definition, since its `step` property is set to `activatePlugin`.
	 *
	 * @param InvalidValue $e
	 *
	 * @return \Swaggest\JsonSchema\Exception\Error|void|null
	 */
	function getSpecificAnyOfError( InvalidValue $e ): \Throwable|null {
		$subSchema = $this->getSubschema( $e->getSchemaPointer() );

		if ( property_exists( $subSchema, '$ref' ) ) {
			$discriminatedDefinition = $this->getSubschema( $subSchema->{'$ref'} );
			if ( property_exists( $discriminatedDefinition, 'discriminator' ) ) {
				$discriminatorField = $discriminatedDefinition->discriminator->propertyName;
				$discriminatorValue = $e->data->$discriminatorField;

				foreach ( $discriminatedDefinition->oneOf as $discriminatorOption ) {
					if ( property_exists( $discriminatorOption, '$ref' ) ) {
						$optionDefinition = $this->getSubschema( $discriminatorOption->{'$ref'} );
						$optionDiscriminatorValue = $optionDefinition->properties->{$discriminatorField}->const;
						if ( $optionDiscriminatorValue === $discriminatorValue ) {
							return $this->findSubErrorForSpecificAnyOfOption( $e->inspect(),
								$discriminatorOption->{'$ref'} );
						}
					}
				}
			}
		}
	}

	private function findSubErrorForSpecificAnyOfOption( \Swaggest\JsonSchema\Exception\Error $e, string $anyOfRef ) {
		if ( $anyOfRef[0] === '#' ) {
			$anyOfRef = substr( $anyOfRef, 1 );
		}
		if ( $e->schemaPointers ) {
			foreach ( $e->schemaPointers as $pointer ) {
				if ( str_starts_with( $pointer, $anyOfRef ) ) {
					return $e;
				}
			}
		}
		if ( ! $e->subErrors ) {
			return;
		}
		foreach ( $e->subErrors as $subError ) {
			$subError = $this->findSubErrorForSpecificAnyOfOption( $subError, $anyOfRef );
			if ( $subError !== null ) {
				return $subError;
			}
		}
	}

	private function getSubschema( $pointer ) {
		if ( $pointer[0] === '#' ) {
			$pointer = substr( $pointer, 1 );
		}
		if ( $pointer[0] !== '/' ) {
			$pointer = substr( $pointer, 1 );
		}
		$path = explode( '/', substr( $pointer, 1 ) );
		$subSchema = $this->blueprintSchema;
		foreach ( $path as $key ) {
			if ( is_numeric( $key ) && ! property_exists( $subSchema, $key ) ) {
				foreach ( $subSchema->anyOf as $v ) {
					if ( is_object( $v ) && property_exists( $v, '$ref' ) ) {
						$subSchema = $v;
						break;
					}
				}
			} else {
				$subSchema = $subSchema->$key;
			}
		}

		return $subSchema;
	}


}
