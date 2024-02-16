<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\ActivatePluginStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/ActivatePluginStep
 */
class ActivatePluginStepBuilder extends ActivatePluginStep implements ClassStructureContract
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
        $properties->step->const = "activatePlugin";
        $properties->pluginPath = Schema::string();
        $properties->pluginPath->description = "Path to the plugin directory as absolute path (/wordpress/wp-content/plugins/plugin-name); or the plugin entry file relative to the plugins directory (plugin-name/plugin-name.php).";
        $properties->pluginName = Schema::string();
        $properties->pluginName->description = "Optional. Plugin name to display in the progress bar.";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->pluginPath,
            self::names()->step,
        );
        $ownerSchema->setFromRef('#/definitions/ActivatePluginStep');
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
     * @param string $pluginPath Path to the plugin directory as absolute path (/wordpress/wp-content/plugins/plugin-name); or the plugin entry file relative to the plugins directory (plugin-name/plugin-name.php).
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setPluginPath($pluginPath)
    {
        $this->pluginPath = $pluginPath;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $pluginName Optional. Plugin name to display in the progress bar.
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setPluginName($pluginName)
    {
        $this->pluginName = $pluginName;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new ActivatePluginStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->continueOnError = $this->recursiveJsonSerialize($this->continueOnError);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->pluginPath = $this->recursiveJsonSerialize($this->pluginPath);
        $dataObject->pluginName = $this->recursiveJsonSerialize($this->pluginName);
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