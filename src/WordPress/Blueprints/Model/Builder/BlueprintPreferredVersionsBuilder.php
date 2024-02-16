<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\BlueprintPreferredVersions;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * The preferred PHP and WordPress versions to use.
 */
class BlueprintPreferredVersionsBuilder extends BlueprintPreferredVersions implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->php = Schema::string();
        $properties->php->description = "The preferred PHP version to use. If not specified, the latest supported version will be used";
        $properties->wp = Schema::string();
        $properties->wp->description = "The preferred WordPress version to use. If not specified, the latest supported version will be used";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->description = "The preferred PHP and WordPress versions to use.";
        $ownerSchema->required = array(
            self::names()->php,
            self::names()->wp,
        );
    }

    /**
     * @param string $php The preferred PHP version to use. If not specified, the latest supported version will be used
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setPhp($php)
    {
        $this->php = $php;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $wp The preferred WordPress version to use. If not specified, the latest supported version will be used
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setWp($wp)
    {
        $this->wp = $wp;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new BlueprintPreferredVersions();
        $dataObject->php = $this->recursiveJsonSerialize($this->php);
        $dataObject->wp = $this->recursiveJsonSerialize($this->wp);
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