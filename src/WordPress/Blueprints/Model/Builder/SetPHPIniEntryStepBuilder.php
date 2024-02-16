<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\SetPHPIniEntryStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/SetPHPIniEntryStep
 */
class SetPHPIniEntryStepBuilder extends SetPHPIniEntryStep implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->progress = ProgressBuilder::schema();
        $properties->step = Schema::string();
        $properties->step->const = "setPhpIniEntry";
        $properties->key = Schema::string();
        $properties->key->description = "Entry name e.g. \"display_errors\"";
        $properties->value = Schema::string();
        $properties->value->description = "Entry value as a string e.g. \"1\"";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->key,
            self::names()->step,
            self::names()->value,
        );
        $ownerSchema->setFromRef('#/definitions/SetPHPIniEntryStep');
    }

    /**
     * @param ProgressBuilder $progress
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setProgress(ProgressBuilder $progress)
    {
        $this->progress = $progress;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $step
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setStep($step)
    {
        $this->step = $step;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $key Entry name e.g. "display_errors"
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
     * @param string $value Entry value as a string e.g. "1"
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new SetPHPIniEntryStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->key = $this->recursiveJsonSerialize($this->key);
        $dataObject->value = $this->recursiveJsonSerialize($this->value);
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