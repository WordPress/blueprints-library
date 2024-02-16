<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\ImportWordPressFilesStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/ImportWordPressFilesStep
 */
class ImportWordPressFilesStepBuilder extends ImportWordPressFilesStep implements ClassStructureContract
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
        $properties->step->const = "importWordPressFiles";
        $properties->wordPressFilesZip = new Schema();
        $properties->wordPressFilesZip->anyOf[0] = Schema::string();
        $properties->wordPressFilesZip->anyOf[1] = VFSReferenceBuilder::schema();
        $properties->wordPressFilesZip->anyOf[2] = LiteralReferenceBuilder::schema();
        $properties->wordPressFilesZip->anyOf[3] = CoreThemeReferenceBuilder::schema();
        $properties->wordPressFilesZip->anyOf[4] = CorePluginReferenceBuilder::schema();
        $properties->wordPressFilesZip->anyOf[5] = UrlReferenceBuilder::schema();
        $properties->wordPressFilesZip->setFromRef('#/definitions/FileReference');
        $properties->pathInZip = Schema::string();
        $properties->pathInZip->description = "The path inside the zip file where the WordPress files are.";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->step,
            self::names()->wordPressFilesZip,
        );
        $ownerSchema->setFromRef('#/definitions/ImportWordPressFilesStep');
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
     * @param string|VFSReferenceBuilder|LiteralReferenceBuilder|CoreThemeReferenceBuilder|CorePluginReferenceBuilder|UrlReferenceBuilder $wordPressFilesZip
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setWordPressFilesZip($wordPressFilesZip)
    {
        $this->wordPressFilesZip = $wordPressFilesZip;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $pathInZip The path inside the zip file where the WordPress files are.
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setPathInZip($pathInZip)
    {
        $this->pathInZip = $pathInZip;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new ImportWordPressFilesStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->continueOnError = $this->recursiveJsonSerialize($this->continueOnError);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->wordPressFilesZip = $this->recursiveJsonSerialize($this->wordPressFilesZip);
        $dataObject->pathInZip = $this->recursiveJsonSerialize($this->pathInZip);
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