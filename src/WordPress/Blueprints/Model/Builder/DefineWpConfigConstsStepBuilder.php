<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\Builder;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;
use Swaggest\JsonSchema\Structure\ClassStructureContract;


/**
 * Built from #/definitions/DefineWpConfigConstsStep
 */
class DefineWpConfigConstsStepBuilder extends DefineWpConfigConstsStep implements ClassStructureContract
{
    use \Swaggest\JsonSchema\Structure\ClassStructureTrait;

    const REWRITE_WP_CONFIG = 'rewrite-wp-config';

    const DEFINE_BEFORE_RUN = 'define-before-run';

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->progress = ProgressBuilder::schema();
        $properties->step = Schema::string();
        $properties->step->const = "defineWpConfigConsts";
        $properties->consts = Schema::object();
        $properties->consts->additionalProperties = new Schema();
        $properties->consts->description = "The constants to define";
        $properties->method = Schema::string();
        $properties->method->enum = array(
            self::REWRITE_WP_CONFIG,
            self::DEFINE_BEFORE_RUN,
        );
        $properties->method->description = "The method of defining the constants. Possible values are:\n\n- rewrite-wp-config: Default. Rewrites the wp-config.php file to                      explicitly call define() with the requested                      name and value. This method alters the file                      on the disk, but it doesn't conflict with                      existing define() calls in wp-config.php.\n- define-before-run: Defines the constant before running the requested                      script. It doesn't alter any files on the disk, but                      constants defined this way may conflict with existing                      define() calls in wp-config.php.";
        $properties->virtualize = Schema::boolean();
        $ownerSchema->type = Schema::OBJECT;
        $ownerSchema->additionalProperties = false;
        $ownerSchema->required = array(
            self::names()->consts,
            self::names()->step,
        );
        $ownerSchema->setFromRef('#/definitions/DefineWpConfigConstsStep');
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
     * @param array $consts The constants to define
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setConsts($consts)
    {
        $this->consts = $consts;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param string $method The method of defining the constants. Possible values are:
     * 
     * - rewrite-wp-config: Default. Rewrites the wp-config.php file to                      explicitly call define() with the requested                      name and value. This method alters the file                      on the disk, but it doesn't conflict with                      existing define() calls in wp-config.php.
     * - define-before-run: Defines the constant before running the requested                      script. It doesn't alter any files on the disk, but                      constants defined this way may conflict with existing                      define() calls in wp-config.php.
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    /**
     * @param bool $virtualize
     * @return $this
     * @codeCoverageIgnoreStart
     */
    public function setVirtualize($virtualize)
    {
        $this->virtualize = $virtualize;
        return $this;
    }
    /** @codeCoverageIgnoreEnd */

    function toDataObject()
    {
        $dataObject = new DefineWpConfigConstsStep();
        $dataObject->progress = $this->recursiveJsonSerialize($this->progress);
        $dataObject->step = $this->recursiveJsonSerialize($this->step);
        $dataObject->consts = $this->recursiveJsonSerialize($this->consts);
        $dataObject->method = $this->recursiveJsonSerialize($this->method);
        $dataObject->virtualize = $this->recursiveJsonSerialize($this->virtualize);
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