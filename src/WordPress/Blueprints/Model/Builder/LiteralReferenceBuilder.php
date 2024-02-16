<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\LiteralReference;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/LiteralReference
 */
class LiteralReferenceBuilder extends LiteralReference implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->resource = Schema::string();
        $properties->resource->description = "Identifies the file resource as a literal file";
        $properties->resource->const = "literal";
        $properties->name = Schema::string();
        $properties->name->description = "The name of the file";
        $properties->contents = Schema::string();
        $properties->contents->description = "The contents of the file";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->resource,
            self::names()->name,
            self::names()->contents,
        );
        $ownerSchema->setFromRef('#/definitions/LiteralReference');
    }

    /**
     * @param string $resource Identifies the file resource as a literal file
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $name The name of the file
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $contents The contents of the file
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new LiteralReference();
        $dataObject->resource = $this->recursiveJsonSerialize($this->resource);
        $dataObject->name = $this->recursiveJsonSerialize($this->name);
        $dataObject->contents = $this->recursiveJsonSerialize($this->contents);
        return $dataObject;
    }

    /**
     * @param mixed $objectMaybe
     */
    private function recursiveJsonSerialize($objectMaybe)
    {
        if ( is_array( $objectMaybe ) ) {
        	return array_map([$this, 'recursiveJsonSerialize'], $objectMaybe);
        } elseif ( $objectMaybe instanceof \Swaggest\JsonSchema\Structure\ClassStructureContract ) {
        	return $objectMaybe->toDataObject();
        } else {
        	return $objectMaybe;
        }
    }
}