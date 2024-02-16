<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\InstallThemeStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/InstallThemeStep
 */
class InstallThemeStepBuilder extends InstallThemeStep implements ClassStructureContract
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
        $properties->step->description = "The step identifier.";
        $properties->step->const = "installTheme";
        $properties->themeZipFile = new Schema();
        $properties->themeZipFile->anyOf[0] = Schema::string();
        $properties->themeZipFile->anyOf[1] = VFSReferenceBuilder::schema();
        $properties->themeZipFile->anyOf[2] = LiteralReferenceBuilder::schema();
        $properties->themeZipFile->anyOf[3] = CoreThemeReferenceBuilder::schema();
        $properties->themeZipFile->anyOf[4] = CorePluginReferenceBuilder::schema();
        $properties->themeZipFile->anyOf[5] = UrlReferenceBuilder::schema();
        $properties->themeZipFile->setFromRef('#/definitions/FileReference');
        $properties->options = InstallThemeStepOptionsBuilder::schema();
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->step,
            self::names()->themeZipFile,
        );
        $ownerSchema->setFromRef('#/definitions/InstallThemeStep');
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
     * @param string|VFSReferenceBuilder|LiteralReferenceBuilder|CoreThemeReferenceBuilder|CorePluginReferenceBuilder|UrlReferenceBuilder $themeZipFile
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setThemeZipFile($themeZipFile)
    {
        $this->themeZipFile = $themeZipFile;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param InstallThemeStepOptionsBuilder $options Optional installation options.
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setOptions(InstallThemeStepOptionsBuilder $options)
    {
        $this->options = $options;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new InstallThemeStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->themeZipFile = $this->recursiveJsonSerialize($this->themeZipFile);
        $dataObject->options = $this->recursiveJsonSerialize($this->options);
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