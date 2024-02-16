<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\CpStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/CpStep
 */
class CpStepBuilder extends CpStep implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->progress = ProgressBuilder::schema();
        $properties->continueOnError = Schema::boolean();
        $properties->step = Schema::string();
        $properties->step->const = "cp";
        $properties->fromPath = Schema::string();
        $properties->fromPath->description = "Source path";
        $properties->toPath = Schema::string();
        $properties->toPath->description = "Target path";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->fromPath,
            self::names()->step,
            self::names()->toPath,
        );
        $ownerSchema->setFromRef('#/definitions/CpStep');
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
     * @param bool $continueOnError
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setContinueOnError($continueOnError)
    {
        $this->continueOnError = $continueOnError;
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
     * @param string $fromPath Source path
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setFromPath($fromPath)
    {
        $this->fromPath = $fromPath;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $toPath Target path
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setToPath($toPath)
    {
        $this->toPath = $toPath;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new CpStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->continueOnError = $this->recursiveJsonSerialize($this->continueOnError);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->fromPath = $this->recursiveJsonSerialize($this->fromPath);
        $dataObject->toPath = $this->recursiveJsonSerialize($this->toPath);
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