<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\WPCLIStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/WPCLIStep
 */
class WPCLIStepBuilder extends WPCLIStep implements ClassStructureContract
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
        $properties->step->const = "wp-cli";
        $properties->command = new Schema();
        $properties->command->anyOf[0] = Schema::string();
        $propertiesCommandAnyOf1 = Schema::arr();
        $propertiesCommandAnyOf1->items = Schema::string();
        $properties->command->anyOf[1] = $propertiesCommandAnyOf1;
        $properties->command->description = "The WP CLI command to run.";
        $properties->wpCliPath = Schema::string();
        $properties->wpCliPath->description = "wp-cli.phar path";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->command,
            self::names()->step,
        );
        $ownerSchema->setFromRef('#/definitions/WPCLIStep');
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
     * @param string|string[]|array $command The WP CLI command to run.
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $wpCliPath wp-cli.phar path
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setWpCliPath($wpCliPath)
    {
        $this->wpCliPath = $wpCliPath;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new WPCLIStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->continueOnError = $this->recursiveJsonSerialize($this->continueOnError);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->command = $this->recursiveJsonSerialize($this->command);
        $dataObject->wpCliPath = $this->recursiveJsonSerialize($this->wpCliPath);
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