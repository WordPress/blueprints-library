<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Context;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\FileInfoData;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * @method static FileInfoDataBuilder|float[] import($data, Context $options = null)
 */
class FileInfoDataBuilder extends FileInfoData implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->bYTESPERELEMENT = Schema::number();
        $ownerSchema->addPropertyMapping('BYTES_PER_ELEMENT', self::names()->bYTESPERELEMENT);
        $properties->buffer = FileInfoDataBufferBuilder::schema();
        $properties->byteLength = Schema::number();
        $properties->byteOffset = Schema::number();
        $properties->length = Schema::number();
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = Schema::number();
        $ownerSchema->required = array(
            'BYTES_PER_ELEMENT',
            self::names()->buffer,
            self::names()->byteLength,
            self::names()->byteOffset,
            self::names()->length,
        );
    }

    /**
     * @param float $bYTESPERELEMENT
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setBYTESPERELEMENT($bYTESPERELEMENT)
    {
        $this->bYTESPERELEMENT = $bYTESPERELEMENT;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param FileInfoDataBufferBuilder $buffer
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setBuffer(FileInfoDataBufferBuilder $buffer)
    {
        $this->buffer = $buffer;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param float $byteLength
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setByteLength($byteLength)
    {
        $this->byteLength = $byteLength;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param float $byteOffset
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setByteOffset($byteOffset)
    {
        $this->byteOffset = $byteOffset;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param float $length
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @return float[]
     * @codeCoverageIgnoreStart
     */
    public function getAdditionalPropertyValues()
    {
        $result = array();
        if (!$names = $this->getAdditionalPropertyNames()) {
            return $result;
        }
        foreach ($names as $name) {
            $result[$name] = $this->$name;
        }
        return $result;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $name
     * @param float $value
     * @return self
     * @codeCoverageIgnoreStart
     */
    public function setAdditionalPropertyValue($name, $value)
    {
        $this->addAdditionalPropertyName($name);
        $this->{$name} = $value;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new FileInfoData();
        $dataObject->bYTESPERELEMENT = $this->recursiveJsonSerialize($this->bYTESPERELEMENT);
        $dataObject->buffer = $this->recursiveJsonSerialize($this->buffer);
        $dataObject->byteLength = $this->recursiveJsonSerialize($this->byteLength);
        $dataObject->byteOffset = $this->recursiveJsonSerialize($this->byteOffset);
        $dataObject->length = $this->recursiveJsonSerialize($this->length);
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