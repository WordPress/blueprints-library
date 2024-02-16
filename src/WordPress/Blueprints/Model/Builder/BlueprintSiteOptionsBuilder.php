<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Context;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\BlueprintSiteOptions;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * WordPress site options to define
 * @method static BlueprintSiteOptionsBuilder|string[] import($data, Context $options = null)
 */
class BlueprintSiteOptionsBuilder extends BlueprintSiteOptions implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->blogname = Schema::string();
        $properties->blogname->description = "The site title";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = Schema::string();
        $ownerSchema->description = "WordPress site options to define";
    }

    /**
     * @param string $blogname The site title
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setBlogname($blogname)
    {
        $this->blogname = $blogname;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @return string[]
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
     * @param string $value
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
        $dataObject = new BlueprintSiteOptions();
        $dataObject->blogname = $this->recursiveJsonSerialize($this->blogname);
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