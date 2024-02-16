<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\FileInfo;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/FileInfo
 */
class FileInfoBuilder extends FileInfo implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->key = Schema::string();
        $properties->name = Schema::string();
        $properties->type = Schema::string();
        $properties->data = FileInfoDataBuilder::schema();
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->key,
            self::names()->name,
            self::names()->type,
            self::names()->data,
        );
        $ownerSchema->setFromRef('#/definitions/FileInfo');
    }

    /**
     * @param string $key
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setKey($key)
    {
        $this->key = $key;
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
     * @param FileInfoDataBuilder|float[] $data
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new FileInfo();
        $dataObject->key = $this->recursiveJsonSerialize($this->key);
        $dataObject->name = $this->recursiveJsonSerialize($this->name);
        $dataObject->type = $this->recursiveJsonSerialize($this->type);
        $dataObject->data = $this->recursiveJsonSerialize($this->data);
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