<?php

namespace WordPress\Blueprints;

use Opis\JsonSchema\Errors\ErrorFormatter;
use WordPress\Blueprints\Model\BlueprintBuilder;
use WordPress\Blueprints\Model\DataClass\Blueprint;

// TODO Review class
class BlueprintParser
{

    protected BlueprintValidator $validator;
    protected BlueprintMapper $mapper;

    public function __construct(
        BlueprintValidator $validator,
        BlueprintMapper    $mapper
    ) {
        $this->mapper = $mapper;
        $this->validator = $validator;
    }

    public function parse($rawBlueprint)
    {
        if (is_string($rawBlueprint)) {
            return $this->fromJson($rawBlueprint);
        } elseif ($rawBlueprint instanceof Blueprint) {
            return $this->fromBlueprint($rawBlueprint);
        } elseif ($rawBlueprint instanceof BlueprintBuilder) {
            return $this->fromBlueprint($rawBlueprint->toBlueprint());
        } elseif ($rawBlueprint instanceof \stdClass) {
            return $this->fromObject($rawBlueprint);
        }
        throw new \InvalidArgumentException(
            'Unsupported $rawBlueprint type. Use a JSON string, a parsed JSON object, or a BlueprintBuilder instance.'
        );
    }
    // TODO Evaluate waring: missing function's return type
    public function fromJson($json)
    {
        // TODO Evaluate warning: 'ext-json' is missing in composer.json
        return $this->fromObject(json_decode($json, false));
    }

    public function fromObject(object $data)
    {
        $result = $this->validator->validate($data);
        if (!$result->isValid()) {
            print_r((new ErrorFormatter())->format($result->error()));
            die();
        }

        return $this->mapper->map($data);
    }

    public function fromBlueprint(Blueprint $blueprint)
    {
        $result = $this->validator->validate($blueprint);
        if (!$result->isValid()) {
            print_r((new ErrorFormatter())->format($result->error()));
//          $errorReport = [
//              "dataPointer"   => $rootError->getDataPointer(),
//              "schemaPointer" => $rootError->getSchemaPointer(),
//              "Message"       => $rootError->getMessage(),
//              "Data"          => $rootError->data,
//              "Constraint"    => $rootError->constraint,
//          ];

//          $specificError = $this->getSpecificAnyOfError( $rootError );
//          if ( $specificError ) {
//              throw $specificError;
//          }
//          throw $rootError;
            die();
        }

        return $blueprint;
    }

//    /**
//     * Narrows down ambiguous anyOf errors using the discriminator property.
//     *
//     * When one of the `anyOf` inputs doesn't match the schema, Swaggest\JsonSchema will return as many errors,
//     * as there are `anyOf` options. Sometimes that means 26 errors to sieve through. For example:
//     *
//     * ```
//     *  No valid results for oneOf {
//     *  0: Enum failed, enum: ["a"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[0]
//     *  1: Enum failed, enum: ["b"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[1]
//     *  2: No valid results for anyOf {
//     *  0: Enum failed, enum: ["c"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]->anyOf[0]
//     *  1: Enum failed, enum: ["d"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]->anyOf[1]
//     *  2: Enum failed, enum: ["e"], data: "f" at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]->anyOf[2]
//     *  } at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo->oneOf[2]->$ref[#/cde]
//     *  } at #->properties:root->patternProperties[^[a-zA-Z0-9_]+$]:zoo
//     * ```
//     *
//     * It's highly impractical to reason about that much output, so we narrow down the error to the specific `anyOf` option that failed.
//     *
//     * This function uses the discriminator property to find the specific `anyOf` option that failed.
//     * For example, if the data looks like this:
//     *
//     * ```
//     * {
//     *     "steps": [
//     *          { "step": "activatePlugin", "plugin": null },
//     *       ]
//     * }
//     * ```
//     *
//     * And the schema looks like this:
//     *
//     * ```
//     * "StepDefinition": {
//     *     "type": "object",
//     *     "discriminator": {
//     *         "propertyName": "step"
//     *     },
//     *     "oneOf": [
//     *         { "$ref": "#/definitions/ActivatePluginStep" },
//     *         { "$ref": "#/definitions/ActivateThemeStep" },
//     * ```
//     *
//     * This function will go through all the errors reported by Swaggest\JsonSchema and find the one associated
//     * with the `ActivatePluginStep` definition, since its `step` property is set to `activatePlugin`.
//     *
//     * @param InvalidValue $e
//     *
//     * @return Error|void|null
//     */
//    function getSpecificAnyOfError(InvalidValue $e): \Throwable|null
//    {
//        $subSchema = $this->getSubschema($e->getSchemaPointer());
//
//        if (property_exists($subSchema, '$ref')) {
//            $discriminatedDefinition = $this->getSubschema($subSchema->{'$ref'});
//            if (property_exists($discriminatedDefinition, 'discriminator')) {
//                $discriminatorField = $discriminatedDefinition->discriminator->propertyName;
//                $discriminatorValue = $e->data->$discriminatorField;
//
//                foreach ($discriminatedDefinition->oneOf as $discriminatorOption) {
//                    if (property_exists($discriminatorOption, '$ref')) {
//                        $optionDefinition = $this->getSubschema($discriminatorOption->{'$ref'});
//                        $optionDiscriminatorValue = $optionDefinition->properties->{$discriminatorField}->const;
//                        if ($optionDiscriminatorValue === $discriminatorValue) {
//                            return $this->findSubErrorForSpecificAnyOfOption(
//                                $e->inspect(),
//                                $discriminatorOption->{'$ref'}
//                            );
//                        }
//                    }
//                }
//            }
//        }
//    }
//

// TODO Review logic in this method (might've been corrupted during downgrade)

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
