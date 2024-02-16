<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\PHPRequestFilesAdditionalProperties;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


class PHPRequestFilesAdditionalPropertiesBuilder extends PHPRequestFilesAdditionalProperties implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->size = Schema::number();
        $properties->type = Schema::string();
        $properties->lastModified = Schema::number();
        $properties->name = Schema::string();
        $properties->webkitRelativePath = Schema::string();
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->lastModified,
            self::names()->name,
            self::names()->size,
            self::names()->type,
            self::names()->webkitRelativePath,
        );
    }

    /**
     * @param float $size
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $type
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param float $lastModified
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $name
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
     * @param string $webkitRelativePath
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setWebkitRelativePath($webkitRelativePath)
    {
        $this->webkitRelativePath = $webkitRelativePath;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new PHPRequestFilesAdditionalProperties();
        $dataObject->size = $this->recursiveJsonSerialize($this->size);
        $dataObject->type = $this->recursiveJsonSerialize($this->type);
        $dataObject->lastModified = $this->recursiveJsonSerialize($this->lastModified);
        $dataObject->name = $this->recursiveJsonSerialize($this->name);
        $dataObject->webkitRelativePath = $this->recursiveJsonSerialize($this->webkitRelativePath);
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