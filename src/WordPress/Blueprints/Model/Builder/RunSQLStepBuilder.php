<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\RunSQLStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/RunSQLStep
 */
class RunSQLStepBuilder extends RunSQLStep implements ClassStructureContract
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
        $properties->step->description = "The step identifier.";
        $properties->step->const = "runSql";
        $properties->sql = new Schema();
        $properties->sql->anyOf[0] = Schema::string();
        $properties->sql->anyOf[1] = VFSReferenceBuilder::schema();
        $properties->sql->anyOf[2] = LiteralReferenceBuilder::schema();
        $properties->sql->anyOf[3] = CoreThemeReferenceBuilder::schema();
        $properties->sql->anyOf[4] = CorePluginReferenceBuilder::schema();
        $properties->sql->anyOf[5] = UrlReferenceBuilder::schema();
        $properties->sql->setFromRef('#/definitions/FileReference');
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->sql,
            self::names()->step,
        );
        $ownerSchema->setFromRef('#/definitions/RunSQLStep');
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
     * @param string $step The step identifier.
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
     * @param string|VFSReferenceBuilder|LiteralReferenceBuilder|CoreThemeReferenceBuilder|CorePluginReferenceBuilder|UrlReferenceBuilder $sql
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new RunSQLStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->continueOnError = $this->recursiveJsonSerialize($this->continueOnError);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->sql = $this->recursiveJsonSerialize($this->sql);
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