<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\UnzipStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/UnzipStep
 */
class UnzipStepBuilder extends UnzipStep implements ClassStructureContract
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
        $properties->step->const = "unzip";
        $properties->zipFile = new Schema();
        $properties->zipFile->anyOf[0] = Schema::string();
        $properties->zipFile->anyOf[1] = VFSReferenceBuilder::schema();
        $properties->zipFile->anyOf[2] = LiteralReferenceBuilder::schema();
        $properties->zipFile->anyOf[3] = CoreThemeReferenceBuilder::schema();
        $properties->zipFile->anyOf[4] = CorePluginReferenceBuilder::schema();
        $properties->zipFile->anyOf[5] = UrlReferenceBuilder::schema();
        $properties->zipFile->setFromRef('#/definitions/FileReference');
        $properties->zipPath = Schema::string();
        $properties->zipPath->description = "The path of the zip file to extract";
        $properties->extractToPath = Schema::string();
        $properties->extractToPath->description = "The path to extract the zip file to";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->extractToPath,
            self::names()->step,
        );
        $ownerSchema->setFromRef('#/definitions/UnzipStep');
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
     * @param string|VFSReferenceBuilder|LiteralReferenceBuilder|CoreThemeReferenceBuilder|CorePluginReferenceBuilder|UrlReferenceBuilder $zipFile
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setZipFile($zipFile)
    {
        $this->zipFile = $zipFile;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $zipPath The path of the zip file to extract
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setZipPath($zipPath)
    {
        $this->zipPath = $zipPath;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $extractToPath The path to extract the zip file to
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setExtractToPath($extractToPath)
    {
        $this->extractToPath = $extractToPath;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new UnzipStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->continueOnError = $this->recursiveJsonSerialize($this->continueOnError);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->zipFile = $this->recursiveJsonSerialize($this->zipFile);
        $dataObject->zipPath = $this->recursiveJsonSerialize($this->zipPath);
        $dataObject->extractToPath = $this->recursiveJsonSerialize($this->extractToPath);
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