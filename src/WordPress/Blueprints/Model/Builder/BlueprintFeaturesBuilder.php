<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\BlueprintFeatures;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


class BlueprintFeaturesBuilder extends BlueprintFeatures implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->networking = Schema::boolean();
        $properties->networking->description = "Should boot with support for network request via wp_safe_remote_get?";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
    }

    /**
     * @param bool $networking Should boot with support for network request via wp_safe_remote_get?
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setNetworking($networking)
    {
        $this->networking = $networking;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new BlueprintFeatures();
        $dataObject->networking = $this->recursiveJsonSerialize($this->networking);
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