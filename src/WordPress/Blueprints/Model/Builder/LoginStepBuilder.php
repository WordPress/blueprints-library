<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\LoginStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/LoginStep
 */
class LoginStepBuilder extends LoginStep implements ClassStructureContract
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
        $properties->step->const = "login";
        $properties->username = Schema::string();
        $properties->username->description = "The user to log in as. Defaults to 'admin'.";
        $properties->password = Schema::string();
        $properties->password->description = "The password to log in with. Defaults to 'password'.";
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->step,
        );
        $ownerSchema->setFromRef('#/definitions/LoginStep');
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
     * @param string $username The user to log in as. Defaults to 'admin'.
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $password The password to log in with. Defaults to 'password'.
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new LoginStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->username = $this->recursiveJsonSerialize($this->username);
        $dataObject->password = $this->recursiveJsonSerialize($this->password);
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