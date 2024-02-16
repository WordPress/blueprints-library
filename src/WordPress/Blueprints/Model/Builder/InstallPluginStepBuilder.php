<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/InstallPluginStep
 */
class InstallPluginStepBuilder extends InstallPluginStep implements ClassStructureContract
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
        $properties->step->const = "installPlugin";
        $properties->pluginZipFile = new Schema();
        $properties->pluginZipFile->anyOf[0] = VFSReferenceBuilder::schema();
        $properties->pluginZipFile->anyOf[1] = LiteralReferenceBuilder::schema();
        $properties->pluginZipFile->anyOf[2] = CoreThemeReferenceBuilder::schema();
        $properties->pluginZipFile->anyOf[3] = CorePluginReferenceBuilder::schema();
        $properties->pluginZipFile->anyOf[4] = UrlReferenceBuilder::schema();
        $properties->pluginZipFile->setFromRef('#/definitions/FileReference');
        $properties->options = InstallPluginOptionsBuilder::schema();
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->pluginZipFile,
            self::names()->step,
        );
        $ownerSchema->setFromRef('#/definitions/InstallPluginStep');
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
     * @param VFSReferenceBuilder|LiteralReferenceBuilder|CoreThemeReferenceBuilder|CorePluginReferenceBuilder|UrlReferenceBuilder $pluginZipFile
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setPluginZipFile($pluginZipFile)
    {
        $this->pluginZipFile = $pluginZipFile;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param InstallPluginOptionsBuilder $options
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setOptions(InstallPluginOptionsBuilder $options)
    {
        $this->options = $options;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new InstallPluginStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->pluginZipFile = $this->recursiveJsonSerialize($this->pluginZipFile);
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