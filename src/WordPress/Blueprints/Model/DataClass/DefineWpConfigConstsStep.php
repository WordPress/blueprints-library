<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\ProgressBuilder;


class DefineWpConfigConstsStep
{
    /** @var ProgressBuilder */
    public $progress;

    /** @var string */
    public $step = 'defineWpConfigConsts';

    /** @var array The constants to define */
    public $consts;

    /**
     * @var string The method of defining the constants. Possible values are:

     * - rewrite-wp-config: Default. Rewrites the wp-config.php file to                      explicitly call define() with the requested                      name and value. This method alters the file                      on the disk, but it doesn't conflict with                      existing define() calls in wp-config.php.
     * - define-before-run: Defines the constant before running the requested                      script. It doesn't alter any files on the disk, but                      constants defined this way may conflict with existing                      define() calls in wp-config.php.
     */
    public $method;

    /** @var bool */
    public $virtualize;
}